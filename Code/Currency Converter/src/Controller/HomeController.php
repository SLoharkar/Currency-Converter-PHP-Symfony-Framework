<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\HomeService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class HomeController extends AbstractController{

    private $homeService;
    private $logger;


    /**
     * Constructor for the HomeController class.
     *
     * This constructor initializes the controller with the required services.
     * 
     * @param HomeService $homeService The service responsible for home-related operations.
     * @param LoggerInterface $logger Logger service for handling log messages.
    */
    public function __construct(HomeService $homeService, LoggerInterface $logger){
        $this->homeService = $homeService;
        $this->logger = $logger;
    }


    /**
     * Home Page Route
     *
     * This route handles requests to the home page of the application.
     *
     * @Route("/", name="home")
     *
     * @return The rendered homepage view template.
    */
    public function home(){
        return $this->render("homepage.html.twig");
    }


    /**
     * Handles the login page request.
     *
     * This route processes the login page where users can enter their credentials.
     * It validates the client's IP address, logs the attempted login, and handles any authentication errors.
     *
     * @Route("/login", name="login")
     *
     * @param Request $request The current HTTP request object
     * @param AuthenticationUtils $authenticationUtils Utility for retrieving login errors and previous username
     * @return Response The rendered login page template
    */
    public function login(Request $request,AuthenticationUtils $authenticationUtils): Response {

        $this->logger->info('Client IP address attempting to log in: ' . $request->getClientIp());
        
        // Check if the client's IP address is authorized using the homeService         
        if (!$this->homeService->validateIp($request->getClientIp())){
            $this->addFlash('error', 'Unauthorized IP address.');
            return $this->redirectToRoute('home');
        }

        // Get the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Get the last authentication error, if any
        $error = $authenticationUtils->getLastAuthenticationError();  
        
        // Log the authentication error if there is one
        if ($error){
            $this->logger->error('Authentication error: ' . $error->getMessageKey());
        }

        // Render the login page template with last username and error (if any)
        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }


    /**
     * Redirects the user to the appropriate dashboard based on their roles.
     *
     * This route handles requests to the /dashboard URL. It checks the user's roles and redirects them
     * to either the admin dashboard or the currency converter page based on their assigned roles.
     * If the user has neither ROLE_ADMIN nor ROLE_USER, it renders the login page.
     *
     * @Route("/dashboard", name="dashboard")
     *
     * @return The redirection to the appropriate route or the rendered login page template.
    */
    public function redirectToDashboard(){

        $this->logger->info('User is accessing the /dashboard route.');

        // Check if the user has the 'ROLE_ADMIN' role
        if ($this->isGranted('ROLE_ADMIN')){
            $this->logger->info('User with ROLE_ADMIN is being redirected to the admin_dashboard route.');
            return $this->redirectToRoute('admin_dashboard');
        }

        // Check if the user has the 'ROLE_USER' role
        if ($this->isGranted('ROLE_USER')){
            $this->logger->info('User with ROLE_USER is being redirected to the currency_converter route.');
            return $this->redirectToRoute('currency_converter');
        }

        return $this->render('login.html.twig');
    }


    /**
     * @Route("/logout", name="logout")
    */
    public function logout(){
        $this->logger->info('User logged out successfully.');
    }


    /**
     * Handles the user registration page and form submission.
     *
     * This route processes the registration page where users can create a new account. 
     * It validates the POST request, attempts to register the user, logs the result, 
     * and handles success or failure by displaying a flash message and redirecting to the login page.
     *
     * @Route("/register", name="register")
     *
     * @param Request $request The current HTTP request object
     * @return Response The rendered registration page template or redirection to the login page
    */
    public function register(Request $request): Response {

        if ($request->isMethod('POST')){

            $username = $request->request->get('username');
            $password = $request->request->get('password');

                try {
                    $this->homeService->register($username, $password);
                    $this->logger->info('User registration successful for username: ' . htmlspecialchars($username));
                    $this->addFlash('success', "Registration successful! You can now log in as ".htmlspecialchars($username));                
                    return $this->redirectToRoute('login');
                }
                catch (\Exception $e){
                    $this->logger->error('Registration failed for username: ' . htmlspecialchars($username) . ' - Error: ' . $e->getMessage());
                    $this->addFlash('error', "Registration failed: " . $e->getMessage());
                    return $this->render('register.html.twig', [], new Response('',Response::HTTP_INTERNAL_SERVER_ERROR));
                }
        }
        return $this->render('register.html.twig', [], new Response('',Response::HTTP_OK));
    }


    /**
     * Forgot Password Route
     *
     * This route handles the process for users to request a password reset.
     * It processes the form submission for resetting the password.
     *
     * @Route("/forgot_password", name="forgot_password")
     *
     * @param Request $request The current HTTP request object.
     * 
     * @return Response The rendered forgot password view or a redirect response based on the request method.
    */
    public function forgot_password(Request $request): Response {

        if ($request->isMethod('POST')){

            $username = $request->request->get('username');
            $extPassword = $request->request->get('ext_password');
            $newPassword = $request->request->get('new_password');
    
            $this->logger->info('Password reset request received for username: ' . htmlspecialchars($username));

            try {
                $this->homeService->reset_password($username,$extPassword,$newPassword);
                $this->logger->info('Password reset successfully for username: ' . htmlspecialchars($username));
                $this->addFlash('success', "Password reset successfully");
                return $this->redirectToRoute('login');
            }
            catch(\Exception $e){
                $this->logger->error('Password reset failed for username: ' . htmlspecialchars($username) . '. Error: ' . $e->getMessage());
                $this->addFlash('error', $e->getMessage());
                return $this->render('forgot_password.html.twig', [], new Response('',Response::HTTP_BAD_REQUEST));
            }
        }
        return $this->render('forgot_password.html.twig');       
    }  
}
?>