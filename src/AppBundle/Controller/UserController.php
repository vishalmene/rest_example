<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;

class UserController extends FOSRestController
{
	/**
     * @Rest\Get("/user")
     * Return the overall user list.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall User List",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     * @return View 
     */
    public function getAction()
    {
      $restresult = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if ($restresult === null) {
          return new View("there are no users exist", Response::HTTP_NOT_FOUND);
     	}

        $view = View::create();
        $view->setData($restresult)->setStatusCode(200);
        return $view;
    }
	/**
	 * @Rest\Get("/user/{id}")
	 *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an user identified by userid",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param integer $id userid
     *
     * @return View
	 */
	 public function idAction($id)
	 {
	   $singleresult = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
	   if ($singleresult === null) {
	   return new View("user not found", Response::HTTP_NOT_FOUND);
	   }
	    $view = View::create();
        $view->setData($singleresult)->setStatusCode(200);
        return $view;
	 }    
	/**
	 * @Rest\Post("/user/")
	 * Create a User from the submitted data.<br/>
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new user from the submitted data.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="name", nullable=false, strict=true, description="Name.")
     * @RequestParam(name="role", nullable=false, strict=true, description="Role.")
     *
     * @return View
     */
	 public function postAction(Request $request)
	 {
	   $data = new User;
	   $name = $request->get('name');
	   $role = $request->get('role');
		 if(empty($name) || empty($role))
		 {
		   return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE); 
		 } 
	  $data->setName($name);
	  $data->setRole($role);
	  $em = $this->getDoctrine()->getManager();
	  $em->persist($data);
	  $em->flush();
	  return new View("User Added Successfully", Response::HTTP_OK);
	 }
  	/**
	 * @Rest\Put("/user/{id}")
	 * Update a User from the submitted data by ID.<br/>
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates a user from the submitted data by ID.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="id", nullable=false, strict=true, description="UserId.")
     * @RequestParam(name="name", nullable=false, strict=true, description="Name.")
     * @RequestParam(name="role", nullable=false, strict=true, description="Role.")
     *
     * @return View
	 */
	 public function updateAction($id,Request $request)
	 { 
	 $data = new User;
	 $name = $request->get('name');
	 $role = $request->get('role');
	 $sn = $this->getDoctrine()->getManager();
	 $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
	 if (empty($user)) {
	   return new View("user not found", Response::HTTP_NOT_FOUND);
	 } 
	 elseif(!empty($name) && !empty($role)) {
	   $user->setName($name);
	   $user->setRole($role);
	   $sn->flush();
	   return new View("User Updated Successfully", Response::HTTP_OK);
	 }
	 elseif(empty($name) && !empty($role)) {
	   $user->setRole($role);
	   $sn->flush();
	   return new View("role Updated Successfully", Response::HTTP_OK);
	}
	elseif(!empty($name) && empty($role)){
	 $user->setName($name);
	 $sn->flush();
	 return new View("User Name Updated Successfully", Response::HTTP_OK); 
	}
	 else return new View("User name or role cannot be empty", Response::HTTP_NOT_ACCEPTABLE); 
	}	 
	/**
	 * @Rest\Delete("/user/{id}")
	 * Delete an user identified by username/email.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete an user identified by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id userid
     *
     * @return View
	 */
	 public function deleteAction($id)
	 {
		  $data = new User;
		  $sn = $this->getDoctrine()->getManager();
		  $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
		  if (empty($user)) {
		  	return new View("user not found", Response::HTTP_NOT_FOUND);
		  }
		  else {
		   $sn->remove($user);
		   $sn->flush();
		  }
		  return new View("deleted successfully", Response::HTTP_OK);
	 }	

}