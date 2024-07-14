<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AdminService;
use Psr\Log\LoggerInterface;



class AdminController extends AbstractController{
    
    private $adminService;
    private $logger;


    /**
     * Constructor for the AdminController class.
     *
     * This constructor initializes the class with the provided AdminService and LoggerInterface instances.
     * It sets up the adminService to handle administrative operations and the logger to handle logging events and errors.
     *
     * @param AdminService $adminService The service responsible for administrative operations.
     * @param LoggerInterface $logger The service responsible for logging events and errors.
    */
    public function __construct(AdminService $adminService, LoggerInterface $logger){
        $this->adminService = $adminService;
        $this->logger = $logger;
    }


    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     *
     * Displays the admin dashboard.
     *
     * This method handles the request to the admin dashboard route. It retrieves all users from the AdminService
     * and renders the 'admin_dashboard.html.twig' template with the user data.
    */
    public function adminconsole(){

        $this->logger->info('Admin dashboard route accessed.');

        // Retrieve all users using the admin service
        $users = $this->adminService->getAllUsers();

        $this->logger->info('Number of users retrieved: ' . count($users));

        // Render the template with the users
        return $this->render('admin_dashboard.html.twig', [
            'users' => $users,
        ]);
    }


    /**
     * @Route("/admin/user-delete/{id}", name="admin_user_delete")
     *
     * Deletes a user with the specified ID.
     *
     * This method handles the request to delete a user by their ID. It calls the admin service to perform
     * the deletion and provides feedback messages to the user based on the success or failure of the operation.
     * If the deletion is successful, a success message is added to the flash bag. If an error occurs, an error
     * message is added to the flash bag.
     *
     * @param int $id The ID of the user to be deleted.
     * @return The response redirects to the admin dashboard.
    */
    public function adminUserDelete($id){

        $this->logger->info('Request to delete user with ID: ' . $id);

        try {
            $this->adminService->adminUserDelete($id);
            $this->logger->info('User with ID ' . $id . ' deleted successfully.');
            $this->addFlash('success', 'User deleted successfully.');
        } 
        catch (\Exception $e){
            $this->logger->error('Error deleting user with ID ' . $id . ': ' . $e->getMessage());
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('admin_dashboard');
    }


    /**
     * @Route("/admin/user-update/{id}", name="admin_user_update")
     *
     * Handles the user update request for the specified user ID.
     *
     * This method retrieves the user by their ID and renders a form for updating the user's details.
     * If the form is submitted via POST request, it attempts to update the user's information using
     * the admin service and provides feedback messages to the user based on the success or failure of the operation.
     * 
     * If the current admin user attempts to change their own role to 'ROLE_USER', it will log them out and redirect
     * them to the login page.
     *
     * @param Request $request The HTTP request object.
     * @param int $id The ID of the user to be updated.
     * @return The response renders the user update form or redirects to the admin dashboard.
    */
    public function adminUserUpdate(Request $request, $id){

        $this->logger->info('Request to update user with ID: ' . $id);

        // Retrieve the user by their ID
        $user = $this->adminService->findById($id);

        if ($request->isMethod('POST')){

            try {

                // Get user input from the request
                $username = $request->request->get('username');
                $password = $request->request->get('password');
                $roles = $request->request->get('roles', []);

                $this->logger->info('Updating user with ID ' . $id . ' - Username: ' . $username . ', Roles: ' . implode(', ', $roles));

                // Get the existing user ID
                $extId = $user->getId();

                // Perform the user update using the AdminService and check if a logout is required
                $requiresLogout = $this->adminService->adminUserUpdate($username,$password,$roles,$extId);

                $this->logger->info('User with ID ' . $id . ' updated successfully.');

                $this->addFlash('success', 'User updated successfully.');

                // If the role change requires a logout, redirect to the login page
                if ($requiresLogout){
                    $this->logger->info('User with ID ' . $id . ' has changed their own role to ROLE_USER. Redirecting to login page.');
                    $this->addFlash('success', 'Your role has been changed. Please log in again.');
                    return $this->redirectToRoute('login');
                }
                
                return $this->redirectToRoute('admin_dashboard');
            }
            catch(\Exception $e){
                $this->logger->error('Error updating user with ID ' . $id . ': ' . $e->getMessage());
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('admin_update.html.twig', [
            'user' => $user,
        ]);
    }

}
?>