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
        $page = $page ?? 1;
        $paymentsModel = new PaymentsModel(); 
        $perPage = 20;
    
        $payments = $paymentsModel->select('payments.*, bookings.id as booking_id, bookings.status as booking_status')
            ->join('bookings', 'bookings.id = payments.booking_id', 'left') 
            ->where('payments.status', 'Pending')  
            ->paginate($perPage, 'default', $page);  
    
        $data['payments'] = $payments;  
        $data['pager'] = $paymentsModel->pager; 
        $data['currentPage'] = $page;
        $data['resultCount'] = $paymentsModel->where('status', 'Pending')->countAllResults(); 
        $data['perPage'] = $perPage;
    
        return view('admin/paymentRequest', $data);
    }
    
    //Send a payment request (User)
    public function processPayment($bookingId)
    {
        $bookingModel = new Bookings();
        $paymentModel = new PaymentsModel();
        
        $booking = $bookingModel->find($bookingId);
        
        if (!$booking) {
            session()->setFlashdata('error', 'Booking not found!');
            return redirect()->to('/bookings');
        }
        
        if ($booking['status'] !== 'Approved') {
            // If not approved, show an error message
            session()->setFlashdata('error', 'Booking is not approved for payment.');
            return redirect()->to('/bookings');
        }
    
        // Get form data
        $transactionId = $this->request->getPost('transaction_id');
        $paymentProof = $this->request->getFile('payment_proof');
        
        if ($paymentProof->isValid() && !$paymentProof->hasMoved()) {
            $fileData = file_get_contents($paymentProof->getTempName()); // Read the file as binary
            
            if ($fileData !== false) {
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
    
                try {
                    $paymentModel->save($paymentData);
                    session()->setFlashdata('success', 'Your payment request has been submitted for approval.');

                } catch (\Exception $e) {
                    // Log error if insertion fails
                    log_message('error', 'Error saving payment: ' . $e->getMessage());
                    session()->setFlashdata('error', 'There was an error submitting your payment.');
                }
            } else {
                session()->setFlashdata('error', 'Error reading the payment proof file.');
                return redirect()->to(base_url('payment/checkout/' . $bookingId));

            }
        } else {
            session()->setFlashdata('error', 'Invalid payment proof file.');
            return redirect()->to(base_url('payment/checkout/' . $bookingId)); 
        }
        return redirect()->to(base_url('homepage/bookings'));
    }
    
    public function uploadProof($paymentId)
    {
        $file = $this->request->getFile('payment_proof');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file upload');
        }

        $fileContent = file_get_contents($file->getTempName());
        $paymentModel = new PaymentsModel();
        $paymentModel->update($paymentId, ['payment_proof' => $fileContent]);

        return redirect()->to('/payments/view/' . $paymentId)->with('success', 'Proof of payment uploaded');
    }

    //To view a payment data for user, 
    public function paymentUser($bookingId)
    {
        $bookingsModel = new Bookings();
        $paymentsModel = new PaymentsModel();
        $booking = $bookingsModel->find($bookingId);

        if (!$booking) {
            // If booking not found, show error message
            session()->setFlashdata('error', 'Booking not found.');
            return redirect()->to(base_url('/bookings'));
        }

        $userId = session()->get('id');
        if ($booking['user_id'] !== $userId) {
            session()->setFlashdata('error', 'You do not have permission to access this payment page.');
            return redirect()->to('/bookings');
        }

        if ($booking['status'] !== 'Approved') {
            session()->setFlashdata('error', 'Only approved bookings can be paid.');
            return redirect()->to('/bookings');
        }

        $payment = $paymentsModel->where('booking_id', $bookingId)->first();

        return view('customer/payment', [
            'booking' => $booking,
            'payment' => $payment
        ]);
    }


    //View payment data; for admin it includes controls
    public function viewPayment($bookingId)
    {
        $bookingModel = new Bookings();
        $paymentModel = new PaymentsModel();
    
        $booking = $bookingModel->find($bookingId);
        $payment = $paymentModel->where('booking_id', $bookingId)->first();
    
        // Check if the booking exists
        if (!$booking) {
            session()->setFlashdata('error', 'Booking not found!');
            return redirect()->to('/bookings');
        }
    
        // Get the user's privilege from session
        $privilege = session()->get('privilege');
 
        if ($privilege === 'Admin') {
            return view('admin/viewPayment', [
                'booking' => $booking,
                'payment' => $payment,
            ]);
        } else  {
            return view('customer/viewPayment', [
                'booking' => $booking,
                'payment' => $payment,
            ]);
        } 
    }

    public function downloadProof($paymentId)
    {
        $paymentModel = new PaymentsModel();
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

        $paymentModel->update($paymentId, ['status' => 'Approved']);
        
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
        $reservationUpdated = $schedulesModel->cancelReservation($bookingId);
    
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
        $page = $page ?? 1; 
        $paymentsModel = new PaymentsModel();
        $perPage = 20;
    
        $payments = $paymentsModel->select('payments.*, bookings.id as booking_id, bookings.status as booking_status')
            ->join('bookings', 'bookings.id = payments.booking_id', 'left') 
            ->where('payments.status', 'Waiting for Refund') 
            ->paginate($perPage, 'default', $page); 
    
        // Additional data for pagination
        $data['payments'] = $payments; 
        $data['pager'] = $paymentsModel->pager;  
        $data['currentPage'] = $page;
        $data['resultCount'] = $paymentsModel->where('status', 'Pending')->countAllResults(); 
        $data['perPage'] = $perPage;
    
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