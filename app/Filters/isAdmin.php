<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class isAdmin implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */public function before(RequestInterface $request, $arguments = null)
        {
            $session = session();
            $userId = $session->get('id'); // Get user ID from session

            if (!$userId) {
                return redirect()->to(base_url('login'))->with('error', 'You must be logged in to view that page.');
            }

            // Load UserModel to check if the user is an admin
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($userId); // Retrieve user by ID

            if (!$user || $user['privilege'] !== 'Admin') {
                // If user does not exist or is not an admin
                return redirect()->to(base_url('/'))->with('error', 'You do not have permission to access this page.');
            }
        }


    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
