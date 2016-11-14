<?php

namespace AimBundle\Controller;

use AimBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AimBundle\Entity\Letters;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Controller\RegistrationController;



class DefaultController extends FOSRestController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('AimBundle:Default:index.html.twig');
    }

    /**
     * // withdrawal of all sent emails
     * @param $request object
     * @Route("/api/letters/all")
     * @Method("GET")
     * @return json
     */
    public function letterAction(Request $request)
    {
        $token = $request->headers;
        list($key, $val) = each($token);
        $access_token = substr($val["authorization"][0],7);
        $user_data = $this->getUserData($access_token);
        $user_id = 0;
        foreach ($user_data as $value){
            $user_id = $value->getId();
        }

        $letters = $this->getDoctrine()->getRepository('AimBundle:Letters')->findBy(['userId'=> $user_id]);
        $data = json_encode($letters);
        if(!empty($data)) {

            return new Response(json_encode(array('code' => 0, 'message' => 'OK', 'emails' => $data)), 200);
        }
        return new Response(json_encode(array('code' => 1, 'message' => 'Empty', "emails" => [])), 200);
    }

    /**
     * add letters in base and sent it to email destinatins
     * @param $request object
     * @Route("/api/letters/add")
     * @Method("POST")
     * @return json
     */
    public function letterAddAction(Request $request){

        $data = $request->getContent();
        $decode_data = json_decode($data);
        
        $token = $request->headers;
        list($key, $val) = each($token);
        $access_token = substr($val["authorization"][0],7);
        
        $user_data = $this->getUserData($access_token);

        $letter = new Letters();
        $letter_data = $letter->getData($decode_data);

        if(!isset($letter_data['error']) && !empty($decode_data)){
            $arr = array();
            foreach ($letter_data as $key=>$value){
                foreach ($value as $k=>$v){
                    $arr[$k] = $v;
                }

                $letter = new Letters();
                $letter->setUserid($user_data[0]->getId());
                $letter->setFromWhom($arr['from']);
                $letter->setDestination($arr['to']);
                $letter->setSubject($arr['subject']);
                $letter->setMessage($arr['message']);
                $letter->setSuccess('0');
                $letter->setSent('0');

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($letter);
                $em->flush();

//SEND LETTERS
                $msg = \Swift_Message::newInstance();
                $msg->setSubject($letter->getSubject());
                $msg->setTo($letter->getDestination());
                $msg->setBody($letter->getMessage());
                $msg->setContentType("text/html");
                $msg->setCharset("utf-8");
                $msg->setFrom($arr['from']);

                $transport = \Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, "ssl")
                    ->setUsername("testaimtestaim@gmail.com")
                    ->setPassword("testaimtest");

                $mailer = \Swift_Mailer::newInstance($transport);
                $result = $mailer->send($msg);
            }
        }
        else{
            return new Response(json_encode(array('code'=>1,'message'=>'Something is wrong...', 'emails' => [])), 200);
        }
        return new Response(json_encode(array('code'=>0,'message'=>'OK', 'emails' => $letter_data)), 200);
    }

    /**
     * // user data
     * @param $access_token string
     * @return array
     */
    public function getUserData($access_token){
        $user_data = $this->getDoctrine()->getRepository('AimBundle:User')->findBy(['confirmationToken'=> $access_token]);
        return $user_data;
    }

    /**
     * // user login
     * @param $request object
     * @Route("/api/login")
     * @Method("POST")
     * @return json
     */
    public function loginAction(Request $request){
        $request = json_decode($request->getContent());
        $request->password = md5($request->password);
        $user = $this->getDoctrine()->getRepository('AimBundle:User')->findBy(['username'=> $request->username,'password' => $request->password]);
        if($user){
            return new Response(json_encode(array('code'=>0,'message'=>'OK', 'name'=> $user[0]->getUsername(), 'email' => $user[0]->getEmail(), 'token' => $user[0]->getConfirmationToken())), 200);
        }
        return new Response(json_encode(array('code'=>1,'message'=>'Wrong username or password'), 200));
    }

    /**
     * // registration of users
     * @param $request object
     * @Route("/api/register")
     * @Method("POST")
     * @return json
     */
    public function registrationAction(Request $request){
        $request = json_decode($request->getContent());
        if ( empty($request->email) ) return new Response(json_encode(array('code'=>2,'message'=>'Email empty.')), 200);
        if ( empty($request->password) ) return new Response(json_encode(array('code'=>2,'message'=>'Password empty.')), 200);
        $request->password = md5($request->password);
        $request->enabled = 1;

        $users = $this->getDoctrine()->getRepository('AimBundle:User')->findBy(['username'=> $request->username]);
        $email = $this->getDoctrine()->getRepository('AimBundle:User')->findBy(['email'=> $request->email]);
        if($users || $email) return new Response(json_encode(array('code'=>2,'message'=>'This username or email exist.')), 200);

        $create_user = new User();
        $create_user->setUsername($request->username);
        $create_user->setUsernameCanonical(strtolower($request->username));
        $create_user->setEmail($request->email);
        $create_user->setEmailCanonical(strtolower($request->email));
        $create_user->setPassword($request->password);
        $create_user->setConfirmationToken($request->token);
        $create_user->setEnabled($request->enabled);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($create_user);
        $em->flush();

        return new Response(json_encode(array('code'=>1,'message'=>'Registration is successful.')), 200);
    }

    /**
     * // take token for registring users
     * @param $request object
     * @Route("api/token")
     * @Method("GET")
     * @return json
     */
    public function tokenAction(Request $request){
        $reg = $this->checkTokenAction($request);
        $token = $_SESSION['_sf2_attributes']['_csrf/registration'];
        return new Response(json_encode(array('code'=>0,'message'=>'OK', 'token' => $token)), 200);
    }

    /**
     * //put token in the session
     * @param Request $request
     *
     * @return Request
     */
    public function checkTokenAction(Request $request)
    {
        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        /*       if ($form->isSubmitted()) {
                   if ($form->isValid()) {
                       $event = new FormEvent($form, $request);
                       $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                       $userManager->updateUser($user);

                       if (null === $response = $event->getResponse()) {
                           $url = $this->getParameter('fos_user.registration.confirmation.enabled')
                               ? $this->generateUrl('fos_user_registration_confirmed')
                               : $this->generateUrl('fos_user_profile_show');

                           $response = new RedirectResponse($url);
                       }

                       $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                       return $response;
                   }

                   $event = new FormEvent($form, $request);
                   $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

                   if (null !== $response = $event->getResponse()) {
                       return $response;
                   }
               }*/

        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
}
