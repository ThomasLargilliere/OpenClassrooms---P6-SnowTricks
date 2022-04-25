<?php

namespace App\Controller;


use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\ImageTrick;
use App\Entity\VideoTrick;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\TrickType;
use App\Form\UpdateTrickType;
use App\Form\ConnexionType;
use App\Form\InscriptionType;
use App\Form\ProfilType;
use App\Form\CommentType;
use Symfony\Component\Validator\Constraints\All;
use Knp\Component\Pager\PaginatorInterface;

class BlogController extends AbstractController
{
    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, \App\Repository\TrickRepository $repo, PaginatorInterface $paginator): Response
    {
        $tricks = $repo->findAll();

        $query = $repo
            ->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
        ;

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            15
        );
        $pagination->setCustomParameters([
            'align' => 'center',
            'size' => 'small',
            'style' => 'bottom',
        ]);
        $pagination->setParam('_fragment', 'sectionTrick');
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'tricks' => $tricks,
            'pagination' => $pagination
        ]);
    }
    #[Route('/create/trick', name: 'create_trick')]
    public function createTrick(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger){

        $this->denyAccessUnlessGranted('ROLE_USER');

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $featuredPicture = $form->get('featuredPicture')->getData();
            if ($featuredPicture){
                $originalFilename = pathinfo($featuredPicture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$featuredPicture->guessExtension();
                try {
                    $featuredPicture->move(
                        $this->getParameter('featured_picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('failed', 'Erreur lors de l\'upload de l\'image à la une');
                    return $this->redirectToRoute('create_trick');
                }

                $trick->setFeaturedPicture($newFilename);

            }

            $slug = $slugger->slug(strtolower($trick->getTitle()));
            $trick->setSlug($slug);
            $user = $this->getUser();
            $trick->setIdUser($user);
            $trick->setCreatedAt(new \DateTimeImmutable());
            $entityManager = $doctrine->getManager();
            $attachedTrick = $entityManager->merge($trick);
            $entityManager->flush();

            $imagesMedia = $form->get('image')->getData();

            foreach($imagesMedia as $image){
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('images_trick_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    //
                }

                $imageTrick = new ImageTrick();
                $imageTrick->setLink($newFilename);
                $imageTrick->setTrick($attachedTrick);
                $entityManager->persist($imageTrick);
                $entityManager->flush();
            }

            $videosMedia = $form->get('video')->getData();

            foreach ($videosMedia as $video){
                $videoTrick = new VideoTrick();
                $videoTrick->setLink($video);
                $videoTrick->setTrick($attachedTrick);
                $entityManager->persist($videoTrick);
                $entityManager->flush();
            }

            $this->addFlash('success', 'Votre trick a bien été ajouté !');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('blog/create_trick.html.twig', [
            'formTrick' => $form->createView()
        ]);
    }

    #[Route('/trick/{slug}', name: 'trick_show')]
    public function show(Request $request, ManagerRegistry $doctrine, $slug, \App\Repository\TrickRepository $repo, \App\Repository\CommentRepository $repoComment, PaginatorInterface $paginator){
        $trick = $repo->findOneBySlug($slug);
        if (!$trick){
            $this->addFlash('failed', 'Aucun trick trouvé');
            return $this->redirectToRoute('index');
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->denyAccessUnlessGranted('ROLE_USER');

            $user = $this->getUser();

            $comment->setUser($user);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setTrick($trick);
            $entityManager = $doctrine->getManager();
            $entityManager->merge($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Le commentaire a bien été ajouté !');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        $query = $repoComment
            ->createQueryBuilder('c')
            ->where('c.Trick = :trick_id')
            ->setParameter('trick_id', $trick->getId())
        ;

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        $pagination->setCustomParameters([
            'align' => 'center',
            'size' => 'small',
            'style' => 'bottom',
        ]);
        $pagination->setParam('_fragment', 'comments');

        return $this->render('blog/show.html.twig', [
            'trick' => $trick,
            'formComment' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    #[Route('/profil', name: 'profil')]
    public function profil(Request $request, ManagerRegistry $doctrine, \App\Repository\UserRepository $repoUser, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher){
        
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $saveAvatar = $user->getAvatar();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $testPseudo = $repoUser->findByPseudo($user->getPseudo());
            $testEmail = $repoUser->findByEmail($user->getEmail());

            if (
                (count($testPseudo) == 0) || 
                (
                    (count($testPseudo) == 1) && ($testPseudo[0]->getId() == $user->getId())
                ) && 
                (count($testEmail) == 0) || 
                (
                    (count($testEmail) == 1) && ($testEmail[0]->getId() == $user->getId())
                )
            ){
                $entityManager = $doctrine->getManager();
                if (isset($_POST['password1']) && ($_POST['password1'] != "")){
                    if (isset($_POST['password2'])){
                        if ($_POST['password1'] == $_POST['password2']){
                            $user->setPassword(
                                $userPasswordHasher->hashPassword(
                                        $user,
                                        $_POST['password1']
                                    )
                                );
                        } else {
                            $this->addFlash('failed', 'Les mots de passes ne correspondent pas');
                            return $this->redirectToRoute('profil');
                        }
                    } else {
                        $this->addFlash('failed', 'Veuillez remplir le champ de confirmation du nouveau mot de passe');
                        return $this->redirectToRoute('profil'); 
                    }
                }

                $avatarFile = $form->get('avatar')->getData();
                if ($avatarFile){
                    $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();
                    try {
                        $avatarFile->move(
                            $this->getParameter('avatar_directory'),
                            $newFilename
                        );
                        $user->setAvatar($newFilename);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                } else {
                    $user->setAvatar($saveAvatar);
                }

                $user->setPseudo($user->getPseudo());
                $user->setEmail($user->getEmail());
                $user->setFirstName($user->getFirstName());
                $user->setLastName($user->getLastName());
                $entityManager->flush();
                $this->addFlash('success', 'Votre profil a bien été mis à jour');
            } else {
                $this->addFlash('failed', 'L\'email ou le pseudo est déjà utilisé !');
            }
        }

        return $this->render('blog/profil.html.twig', [
            'formProfil' => $form->createView()
        ]);
    }

    #[Route('/update/trick/{slug}', name: 'trick_update')]
    public function update_trick(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, $slug, \App\Repository\TrickRepository $repo, \App\Repository\ImageTrickRepository $repoImage, \App\Repository\VideoTrickRepository $repoVideo){
        
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        
        $trick = $repo->findOneBySlug($slug);

        if (!$trick){
            $this->addFlash('failed', 'Aucun trick trouvé avec ce slug');
            return $this->redirectToRoute('index');
        }

        if ($trick->getIdUser()->getId() != $user->getId()){
            $this->addFlash('failed', 'Ce n\'est pas vous qui avez écris ce trick');
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(UpdateTrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $featuredPicture = $form->get('featuredPicture')->getData();

            if ($featuredPicture){
                $originalFilename = pathinfo($featuredPicture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$featuredPicture->guessExtension();
                try {
                    $featuredPicture->move(
                        $this->getParameter('featured_picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('failed', 'Erreur lors de l\'upload de l\'image à la une');
                    return $this->redirectToRoute('create_trick');
                }

                $trick->setFeaturedPicture($newFilename);

            } else {
                $trick->setFeaturedPicture($_POST['lastFeaturedPicture']);
            }

            $entityManager = $doctrine->getManager();
            $delImage = [];
            if (isset($_POST['delImageTrick'])){ $delImage = $_POST['delImageTrick']; }

            foreach($delImage as $image){
                $bdImage = $repoImage->findOneByLink($image);
                $fichier = $this->getParameter('images_trick_directory') . '/' . $image;
                if(file_exists($fichier)){
                    $entityManager->remove($bdImage);
                    $entityManager->flush();
                    unlink($fichier);
                }
            }

            $delVideo = [];
            if (isset($_POST['delVideoTrick'])){ $delVideo = $_POST['delVideoTrick']; }

            foreach($delVideo as $video){
                $bdVideo = $repoVideo->findOneById($video);
                $entityManager->remove($bdVideo);
                $entityManager->flush();
            }


            $slug = $slugger->slug(strtolower($trick->getTitle()));
            $trick->setSlug($slug);

            foreach($trick->getVideo() as $video){
                $entityVideo = $repoVideo->findOneByLink($video);
            }

            $attachedTrick = $entityManager->merge($trick);
            $entityManager->flush();

            $imagesMedia = $form->get('image')->getData();
            foreach($imagesMedia as $image){
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('images_trick_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    //
                }

                $imageTrick = new ImageTrick();
                $imageTrick->setLink($newFilename);
                $imageTrick->setTrick($attachedTrick);
                $entityManager->persist($imageTrick);
                $entityManager->flush();
            }

            $videosMedia = $form->get('video')->getData();
            foreach ($videosMedia as $video){
                $videoTrick = new VideoTrick();
                $videoTrick->setLink($video);
                $videoTrick->setTrick($attachedTrick);
                $entityManager->persist($videoTrick);
                $entityManager->flush();
            }

            $this->addFlash('success', 'Votre trick a été modifié avec succèss !');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('blog/update_trick.html.twig', [
            'formTrick' => $form->createView(),
            'trick' => $trick
        ]);
    }

    #[Route('/delete/trick/{id}', name: 'trick_delete')]
    public function delete_trick(Request $request, ManagerRegistry $doctrine, \App\Repository\TrickRepository $repo, \App\Repository\ImageTrickRepository $repoImage, \App\Repository\VideoTrickRepository $repoVideo, \App\Repository\CommentRepository $repoComment, $id){
        
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $trick = $repo->findOneById($id);

        if (!$trick){
            $this->addFlash('failed', 'Aucun trick trouvé avec cette ID');
            return $this->redirectToRoute('index');
        }

        if ($trick->getIdUser()->getId() != $user->getId()){
            $this->addFlash('failed', 'Ce n\'est pas vous qui avez écris ce trick');
            return $this->redirectToRoute('index');
        }

        $entityManager = $doctrine->getManager();
        foreach($trick->getImage() as $image){
            $bdImage = $repoImage->findOneById($image->getId());
            $fichier = $this->getParameter('images_trick_directory') . '/' . $image->getLink();
            if(file_exists($fichier)){
                $entityManager->remove($bdImage);
                $entityManager->flush();
                unlink($fichier);
            }
        }

        foreach($trick->getVideo() as $video){
            $bdVideo = $repoVideo->findOneById($video->getId());
            $entityManager->remove($bdVideo);
            $entityManager->flush();
        }

        foreach($trick->getComments() as $comment){
            $bdComment = $repoComment->findOneById($comment->getId());
            $entityManager->remove($bdComment);
            $entityManager->flush();            
        }

        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Trick supprimé avec succès');
        return $this->redirectToRoute('index');
    }
    #[Route('/delete/comment/{id}', name: 'comment_delete')]
    public function delete_comment(Request $request, ManagerRegistry $doctrine, \App\Repository\CommentRepository $repo, \App\Repository\TrickRepository $repoTrick, $id){
        
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $this->getUser();
        $comment = $repo->findOneById($id);
        $trick = $repoTrick->findOneById($comment->getTrick()->getId());

        if ($comment->getUser()->getId() != $user->getId()){
            $this->addFlash('failed', 'Vous n\'êtes pas l\'auteur de ce commentaire');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire supprimé avec succès');
        return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
    }
}
