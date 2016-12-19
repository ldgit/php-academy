<?php

namespace AppBundle\Controller;

use AppBundle\Form\Model\User;
use AppBundle\Form\Type\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ExampleController extends Controller
{
    /**
     * @Route("/example/accommodation/insert")
     */
    public function storingAccommodationAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $place = $entityManager->getRepository('AppBundle:Place')->findOneByName('Split');
        $accommodation = new \AppBundle\Entity\Accommodation();
        $accommodation->setName('orm test');
        $accommodation->setCategory(4);
        $accommodation->setPricePerDay(99.99);
        $accommodation->setPlace($place);

        $entityManager->persist($accommodation);
        $entityManager->flush();

        return $this->render('AppBundle:Welcome:homepage.html.twig');
    }

    /**
     * @Route("/example/accommodation/{accommodationId}/dbal-query-builder")
     */
    public function accommodationDbalQueryBuilderAction($accommodationId)
    {
        $queryBuilder = $this->get('database_connection')->createQueryBuilder();
        $statement = $queryBuilder
            ->select('a.*', 'p.name AS place_name')
            ->from('accommodation', 'a')
            ->join('a', 'place', 'p', 'p.id = a.place_id')
            ->where('a.id = ?')
            ->setParameter(0, $accommodationId)
            ->execute();

        $accommodation = $statement->fetch();
        if(!$accommodation)
            throw $this->createNotFoundException(
                sprintf('Accommodation with id "%s" not found.', $accommodationId));

        return $this->render('AppBundle:Example:accommodationQueryBuilder.html.twig', [
            'accommodation' => $accommodation,
        ]);
    }

    /**
     * @Route("/example/forms/begin")
     */
    public function formsBeginAction(Request $request)
    {
        $user = new User();
        $user->firstname = 'Mate';
        $user->phones = ['4575467457','3454574567','347466567','4575467457','3454574567','347466567'];
        $form = $this->createForm(UserType::class, $user, ['validation_groups' => ['broj', 'tekst']]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            dump($form->getData());
        }

        return $this->render('AppBundle:Example:formsBegin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/example/validation")
     */
    public function validationAction(Request $request)
    {
        $user = new User();
        $user->firstname = '140';

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        return new Response('Nema greške');
    }

    /**
     * @Route("/example/user-in-controller")
     */
    public function userInControllerAction()
    {
        // Ako korisnik nije logiran, bacit ćemo Symfony AccessDeniedException koji će uhvatiti security sistem od
        // Symfony-ja i presumjeriti nas na stranicu za prijavu
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        return new Response('<html><body>Hello ' . $user->getName() . ' ' . $user->getSurname() . '</body></html>');
    }


}
