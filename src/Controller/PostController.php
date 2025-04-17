<?php
// src/Controller/PostController.php
namespace App\Controller;
use App\Entity\User;

use App\Entity\Post;
use App\Form\PostType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Comment;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post', methods: ['GET', 'POST'])]
    public function index(PostRepository $repository, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        $searchTerm = $request->query->get('search');
    
        $query = $repository->createQueryBuilder('p')
            ->where('p.title LIKE :searchTerm OR p.content LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );
    
        // Récupérer l'utilisateur statique dont l'id est 1
        $user = $em->getRepository(User::class)->find(1);
        
        $forms = [];
        foreach ($pagination as $post) {
            $comment = new Comment();
            $comment->setPost($post);
            // Affecter l'utilisateur statique au commentaire
            $user = $em->getRepository(User::class)->find(1);
            $comment->setUser($user);
            $forms[$post->getId()] = $this->createForm(CommentType::class, $comment)->createView();
        }
    
        return $this->render('post/index.html.twig', [
            'posts' => $pagination,
            'forms' => $forms,
            'currentPage' => $pagination->getCurrentPageNumber(),
            'previous' => $pagination->getCurrentPageNumber() > 1 ? $pagination->getCurrentPageNumber() - 1 : null,
            'next' => $pagination->getCurrentPageNumber() < $pagination->getPageCount() ? $pagination->getCurrentPageNumber() + 1 : null,
        ]);
    }
    #[Route('/postAdmin', name: 'app_postAdmin', methods: ['GET', 'POST'])]
    public function indexAdmin(PostRepository $repository, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        $searchTerm = $request->query->get('search');
    
        $query = $repository->createQueryBuilder('p')
            ->where('p.title LIKE :searchTerm OR p.content LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );
    
        // Récupérer l'utilisateur statique dont l'id est 1
        $user = $em->getRepository(User::class)->find(1);
        
        $forms = [];
        foreach ($pagination as $post) {
            $comment = new Comment();
            $comment->setPost($post);
            // Affecter l'utilisateur statique au commentaire
            $user = $em->getRepository(User::class)->find(1);
            $comment->setUser($user);
            $forms[$post->getId()] = $this->createForm(CommentType::class, $comment)->createView();
        }
    
        return $this->render('post/indexAdmin.html.twig', [
            'posts' => $pagination,
            'forms' => $forms,
            'currentPage' => $pagination->getCurrentPageNumber(),
            'previous' => $pagination->getCurrentPageNumber() > 1 ? $pagination->getCurrentPageNumber() - 1 : null,
            'next' => $pagination->getCurrentPageNumber() < $pagination->getPageCount() ? $pagination->getCurrentPageNumber() + 1 : null,
        ]);
    }
    

    #[Route('/add_post', name: 'add_post')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier uploadé depuis le champ imageFile
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Nettoyer le nom de fichier pour éviter des problèmes
                $safeFilename = preg_replace('/[^A-Za-z0-9-_]/', '', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Déplacer le fichier dans le dossier défini dans les paramètres (par exemple, "images_directory")
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur si le déplacement échoue
                    $this->addFlash('error', 'Le téléchargement de l\'image a échoué.');
                }

                // Sauvegarder le nom du fichier dans l'entité (adaptation de ta propriété imageUrls)
                $post->setImageUrls($newFilename);
            }

            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Les autres méthodes (update, delete, comments) restent inchangées
    #[Route('/update_post/{id}', name: 'update_post')]
    public function update(PostRepository $repository, int $id, Request $request, ManagerRegistry $doctrine): Response
    {
        $post = $repository->find($id);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/update.html.twig', [
            'form' => $form->createView(), 
        ]);
    }

    #[Route('/delete_post/{id}', name: 'delete_post')]
    public function delete(PostRepository $repository, int $id, ManagerRegistry $doctrine): Response
    {
        $post = $repository->find($id);
        $em = $doctrine->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('app_post');
    }
 
    #[Route('/{id}/comments', name: 'post_comments', methods: ['GET', 'POST'])]
    public function showComments(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        // Modification ici : utiliser setCreatedAt au lieu de setCreated
        $comment->setCreatedAt(new \DateTime());
        $comment->setPost($post);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('app_post');
        }

        return $this->redirectToRoute('app_post');
    }
    #[Route('/post/{id}/comments', name: 'post_admin_comments', methods: ['POST'])]
public function showCommentsAdmin(Post $post, Request $request, EntityManagerInterface $em): Response
{
    $comment = new Comment();
    $comment->setCreatedAt(new \DateTime());
    $comment->setPost($post);

    $user = $em->getRepository(User::class)->find(1);
    $comment->setUser($user);

    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($comment);
        $em->flush();
    }

    return $this->redirectToRoute('app_postAdmin');
}

#[Route('/comment/delete/{id}', name: 'delete_comment')]
public function deleteComment(Comment $comment, EntityManagerInterface $em): Response
{
    $em->remove($comment);
    $em->flush();
    return $this->redirectToRoute('app_post');
}

#[Route('/postAdmin/comment/delete/{id}', name: 'delete_comment_admin')]
public function deleteCommentAdmin(Comment $comment, EntityManagerInterface $em): Response
{
    $em->remove($comment);
    $em->flush();
    return $this->redirectToRoute('app_postAdmin');
}

#[Route('/comment/update/{id}', name: 'update_comment', methods: ['POST'])]
public function updateComment(Comment $comment, Request $request, EntityManagerInterface $em): Response
{
    $newContent = $request->request->get('comment_content');

    if ($newContent) {
        $comment->setContent($newContent);
        $em->flush();
    }

    return $this->redirectToRoute('app_post');
}
#[Route('/commentAdmin/update/{id}', name: 'update_comment_admin', methods: ['POST'])]
public function updateCommentAdmin(Comment $comment, Request $request, EntityManagerInterface $em): Response
{
    $newContent = $request->request->get('comment_content');

    if ($newContent) {
        $comment->setContent($newContent);
        $em->flush();
    }

    return $this->redirectToRoute('app_post');
}
#[Route('/postAdmin/{id}/comments/view', name: 'view_post_comments_admin', methods: ['GET'])]
public function viewPostCommentsAdmin(Post $post): Response
{
    return $this->render('post/view_comments.html.twig', [
        'post' => $post,
        'comments' => $post->getComments(),
    ]);
}



}
