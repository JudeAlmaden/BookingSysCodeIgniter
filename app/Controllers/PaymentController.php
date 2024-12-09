<?php

namespace App\Controllers;
use App\Models\PaymentsModel;
use App\Models\Bookings;
use App\Models\SchedulesModel;
use CodeIgniter\HTTP\RequestInterface;

class PaymentController extends BaseController
{
    public function index($page = null)
    {
        $page = $page ?? 1;  // Default to page 1 if not set
        $paymentsModel = new PaymentsModel(); // Model for payments
        $perPage = 20;
    
        // Fetch payments with booking details, focusing only on payments
        $payments = $paymentsModel->select('payments.*, bookings.id as booking_id, bookings.status as booking_status')
            ->join('bookings', 'bookings.id = payments.booking_id', 'left')  // Left join to include payments even if no related booking
            ->where('payments.status', 'Pending')  // Filter by pending payment status
            ->paginate($perPage, 'default', $page);  // Paginate results
    
        // Additional data for pagination
        $data['payments'] = $payments;  // Store the fetched payments
        $data['pager'] = $paymentsModel->pager;  // Paginate links using the payments model
        $data['currentPage'] = $page;
        $data['resultCount'] = $paymentsModel->where('status', 'Pending')->countAllResults(); // Count pending payments
        $data['perPage'] = $perPage;
    
        // Return the view with the data
        return view('admin/paymentRequest', $data);
    }
    
    //Send a payment request
    public function processPayment($bookingId)
    {
        // Load the models
        $bookingModel = new Bookings();
        $paymentModel = new PaymentsModel();
        
        // Get the booking details
        $booking = $bookingModel->find($bookingId);
        
        if (!$booking) {
            // If booking not found, redirect with an error message
            session()->setFlashdata('error', 'Booking not found!');
            return redirect()->to('/bookings');
        }
        
        // Check if the booking is approved
        if ($booking['status'] !== 'Approved') {
            // If not approved, show an error message
            session()->setFlashdata('error', 'Booking is not approved for payment.');
            return redirect()->to('/bookings');
        }
    
        // Get form data
        $transactionId = $this->request->getPost('transaction_id');
        
        // Handle file upload for payment proof
        $paymentProof = $this->request->getFile('payment_proof');
        
        if ($paymentProof->isValid() && !$paymentProof->hasMoved()) {
            // Read the file contents (binary data)
            $fileData = file_get_contents($paymentProof->getTempName()); // Read the file as binary
            
            // If the file data is successfully read, save it to the database
            if ($fileData !== false) {
                // Corrected payment data to include booking_id and binary file data for payment proof
                $paymentData = [
                    'user_id' => session()->get('id'),
                    'booking_id' => $booking['id'],
                    'amount' => $booking['price'],
                    'status' => 'Pending',
                    'transaction_id' => $transactionId,
                    'payment_proof' => $fileData, // Save the binary data (BLOB)
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
    
                // Save the payment data to the database
                try {
                    $paymentModel->save($paymentData);
                    // Set a success message
                    session()->setFlashdata('success', 'Your payment request has been submitted for approval.');
                } catch (\Exception $e) {
                    // Log error if insertion fails
                    log_message('error', 'Error saving payment: ' . $e->getMessage());
                    session()->setFlashdata('error', 'There was an error submitting your payment.');
                }
            } else {
                // If reading the file failed, handle the error
                session()->setFlashdata('error', 'Error reading the payment proof file.');
                return redirect()->to(base_url('payment/checkout/' . $bookingId)); // Redirect back to payment page
            }
        } else {
            // If no valid file, handle the error
            session()->setFlashdata('error', 'Invalid payment proof file.');
            return redirect()->to(base_url('payment/checkout/' . $bookingId)); // Redirect back to payment page
        }
    
        // Redirect to the bookings page or payment confirmation page
        return redirect()->to(base_url('homepage/bookings'));
    }
    
    //Called in the payment process to upload a file of payment proof
    public function uploadProof($paymentId)
    {
        $validation =  \Config\Services::validation();
        $file = $this->request->getFile('payment_proof');

        // Validate the file
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file upload');
        }

        // Get the file content
        $fileContent = file_get_contents($file->getTempName());

        // Update the payment record with the file content
        $paymentModel = new PaymentsModel();
        $paymentModel->update($paymentId, ['payment_proof' => $fileContent]);

        return redirect()->to('/payments/view/' . $paymentId)->with('success', 'Proof of payment uploaded');
    }

    public function paymentUser($bookingId)
    {
        // Load the Bookings model
        $bookingsModel = new Bookings();
        $paymentsModel = new PaymentsModel();

        // Fetch booking details based on booking ID
        $booking = $bookingsModel->find($bookingId);

        if (!$booking) {
            // If booking not found, show error message
            session()->setFlashdata('error', 'Booking not found.');
            return redirect()->to(base_url('/bookings'));
        }

        // Check if the user is the one who made the booking
        $userId = session()->get('id');
        if ($booking['user_id'] !== $userId) {
            // If the booking does not belong to the logged-in user, redirect with an error
            session()->setFlashdata('error', 'You do not have permission to access this payment page.');
            return redirect()->to('/bookings');
        }

        // Check if the booking is approved and not yet paid
        if ($booking['status'] !== 'Approved') {
            session()->setFlashdata('error', 'Only approved bookings can be paid.');
            return redirect()->to('/bookings');
        }

        // Get the existing payment status
        $payment = $paymentsModel->where('booking_id', $bookingId)->first();

        // Pass data to view (booking details and payment information)
        return view('customer/payment', [
            'booking' => $booking,
            'payment' => $payment
        ]);
    }

    
    public function viewPayment($bookingId)
    {
        // Load the necessary models
        $bookingModel = new Bookings();
        $paymentModel = new PaymentsModel();

        // Get the booking and payment details
        $booking = $bookingModel->find($bookingId);
        $payment = $paymentModel->where('booking_id', $bookingId)->first();

        // Check if the booking exists
        if (!$booking) {
            session()->setFlashdata('error', 'Booking not found!');
            return redirect()->to('/bookings');
        }

        // Pass data to the view
        return view('customer/viewPayment', [
            'booking' => $booking,
            'payment' => $payment,
        ]);
    }

    public function viewPaymentAdmin($bookingId)
    {
        // Load the necessary models
        $bookingModel = new Bookings();
        $paymentModel = new PaymentsModel();

        // Get the booking and payment details
        $booking = $bookingModel->find($bookingId);
        $payment = $paymentModel->where('booking_id', $bookingId)->first();

        // Check if the booking exists
        if (!$booking) {
            session()->setFlashdata('error', 'Booking not found!');
            return redirect()->to(base_url("dashboard"));
        }

        // Pass data to the view
        return view('admin/viewPayment', [
            'booking' => $booking,
            'payment' => $payment,
        ]);
    }

    public function downloadProof($paymentId)
    {
        // Load the payment model
        $paymentModel = new PaymentsModel();

        // Get the payment details
        $payment = $paymentModel->find($paymentId);

        // Check if the payment exists and has a proof
        if ($payment && $payment['payment_proof']) {
            // Set the appropriate headers to force download
            return $this->response->setHeader('Content-Type', 'application/octet-stream')
                                ->setHeader('Content-Disposition', 'attachment; filename="payment_proof_' . $paymentId . '.png"')
                                ->setBody($payment['payment_proof']);
        } else {
            // If no proof exists, show an error message
            session()->setFlashdata('error', 'No payment proof found for this transaction.');
            return redirect()->to('/bookings');
        }
    }

    public function approve($paymentId)
    {
        $paymentModel = new PaymentsModel();
        $payment = $paymentModel->find($paymentId);

        if (!$payment) {
            session()->setFlashdata('error', 'Payment not found.');
            return redirect()->to(base_url('/dashboard'));
        }

        // Update payment status to 'Approved'
        $paymentModel->update($paymentId, ['status' => 'Approved']);
        
        // Optionally, update the booking status to 'Confirmed' or similar
        $bookingModel = new Bookings();
        $bookingModel->update($payment['booking_id'], ['status' => 'Confirmed']);
        
        session()->setFlashdata('success', 'Payment has been approved.');
        return redirect()->to(base_url('/dashboard/payments/1'));
    }

    public function reject($paymentId)
    {
        $paymentModel = new PaymentsModel();
        $payment = $paymentModel->find($paymentId);
    
        if (!$payment) {
            session()->setFlashdata('error', 'Payment not found.');
            return redirect()->to(base_url('/dashboard'));
        }
    
        // Update payment status to 'Denied'
        $paymentModel->update($paymentId, ['status' => 'Denied']);
        
        // Optionally, update the booking status to 'Cancelled'
        $bookingModel = new Bookings();
        $bookingModel->update($payment['booking_id'], ['status' => 'Cancelled']);
    
        // Call the cancelledReservation function to adjust reservations
        $bookingId = $payment['booking_id'];
        $schedulesModel = new SchedulesModel(); // Assuming the `cancelledReservation` function is in this model
        $reservationUpdated = $schedulesModel->cancelledReservation($bookingId);
    
        // Handle the result of the cancellation
        if ($reservationUpdated) {
            session()->setFlashdata('success', 'Payment has been denied and reservations have been adjusted.');
        } else {
            session()->setFlashdata('error', 'Payment denied, but failed to adjust reservations.');
        }
    
        return redirect()->to(base_url('/dashboard/payments/1'));
    }
    
    //View for refund index
    public function refund($page = null)
    {
        $page = $page ?? 1;  // Default to page 1 if not set
        $paymentsModel = new PaymentsModel(); // Model for payments
        $perPage = 20;
    
        // Fetch payments with booking details, focusing only on payments
        $payments = $paymentsModel->select('payments.*, bookings.id as booking_id, bookings.status as booking_status')
            ->join('bookings', 'bookings.id = payments.booking_id', 'left')  // Left join to include payments even if no related booking
            ->where('payments.status', 'Waiting for Refund')  // Filter by pending payment status
            ->paginate($perPage, 'default', $page);  // Paginate results
    
        // Additional data for pagination
        $data['payments'] = $payments;  // Store the fetched payments
        $data['pager'] = $paymentsModel->pager;  // Paginate links using the payments model
        $data['currentPage'] = $page;
        $data['resultCount'] = $paymentsModel->where('status', 'Pending')->countAllResults(); // Count pending payments
        $data['perPage'] = $perPage;
    
        // Return the view with the data
        return view('admin/refunds', $data);
    }

    public function completeRefund($paymentId)
    {
        $paymentModel = new PaymentsModel();
        $payment = $paymentModel->find($paymentId);
    
        if (!$payment) {
            session()->setFlashdata('error', 'Payment not found.');
            return redirect()->to(base_url('/dashboard'));
        }
    
        // Handle the result of the cancellation
        if ( $paymentModel->update($paymentId, ['status' => 'Completed'])) {
            session()->setFlashdata('success', 'Payment has been marked as completed and the reservation has been updated.');
        } else {
            session()->setFlashdata('error', 'Payment completed, but failed to update the reservation.');
        }
    
        return redirect()->to(base_url('/dashboard/payments/1'));
    }
    
}
