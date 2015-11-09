<?php

namespace MyDrinks\UserInterface\Symfony\AppBundle\Controller;

use MyDrinks\Application\Command\AddRecipeStepCommand;
use MyDrinks\Application\Command\CreateNewRecipeCommand;
use MyDrinks\Application\Command\PublishRecipeCommand;
use MyDrinks\Application\Command\RemoveRecipeCommand;
use MyDrinks\Application\Command\RemoveRecipeImageCommand;
use MyDrinks\Application\Command\RemoveRecipeStepCommand;
use MyDrinks\Application\Command\UpdateRecipeDescriptionCommand;
use MyDrinks\Application\Command\UploadRecipeImageCommand;
use MyDrinks\Application\SearchEngine\Criteria;
use MyDrinks\UserInterface\Symfony\AppBundle\Form\Type\AdminSearchType;
use MyDrinks\UserInterface\Symfony\AppBundle\Form\Type\CreateRecipeType;
use MyDrinks\UserInterface\Symfony\AppBundle\Form\Type\RecipeDescriptionType;
use MyDrinks\UserInterface\Symfony\AppBundle\Form\Type\RecipeImageType;
use MyDrinks\UserInterface\Symfony\AppBundle\Form\Type\RecipeStepType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AdminRecipeController extends Controller
{
    const EXCEPTION_TARGET_URL_KEY = 'my_drinks.exception.target_url';
    
    public function createAction(Request $request)
    {
        $form = $this->createForm(new CreateRecipeType(), new CreateNewRecipeCommand());
        $searchForm = $this->createForm(new AdminSearchType());
        
        $form->handleRequest($request);
        $searchForm->handleRequest($request);
        
        if ($form->isValid()) {
            $this->get('my_drinks.command_bus')->handle($form->getData());
            $slug = $this->get('my_drinks.slug_generator')->generateFrom($form->get('name')->getData());
            
            return $this->redirect($this->generateUrl('admin_recipe_update_description', ['slug' => $slug]));
        }

        $criteria = new Criteria((int) $request->query->get('start', 0), false);
        $criteria->sortBy('name.raw', Criteria::SORT_ASC);
        
        if ($searchForm->isValid()) {
            $criteria->addQuery($searchForm->get('name')->getData());
        } 
        
        $results = $this->get('my_drinks.search_engine')->search($criteria);
        
        return $this->render('admin/recipe/create.html.twig', [
            'search' => $searchForm->createView(),
            'form' => $form->createView(),
            'results' => $results
        ]);
    }

    public function removeAction($slug)
    {
        $command = new RemoveRecipeCommand();
        $command->slug = $slug;

        $this->get('my_drinks.command_bus')->handle($command);

        return $this->redirect($this->generateUrl('admin_recipe_create'));
    }
    
    public function updateDescriptionAction(Request $request, $slug)
    {
        $recipe = $this->get('my_drinks.recipes')->findBySlug($slug);
        if (is_null($recipe)) {
            throw new NotFoundHttpException;
        }
        
        $command = UpdateRecipeDescriptionCommand::createFromRecipe($recipe);
        $command->slug = $slug;
        
        $form = $this->createForm(new RecipeDescriptionType(), $command);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $this->get('my_drinks.command_bus')->handle($form->getData());

            return $this->redirect($this->generateUrl('admin_recipe_add_step', ['slug' => $slug]));
        } 

        return $this->render('admin/recipe/updateDescription.html.twig', [
            'form' => $form->createView(),
            'slug' => $slug
        ]);
    }

    public function addStepAction($slug, Request $request)
    {
        $command = new AddRecipeStepCommand();
        $command->slug = $slug;
        $form = $this->createForm(new RecipeStepType(), $command);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('my_drinks.command_bus')->handle($form->getData());

            return $this->redirect($this->generateUrl('admin_recipe_add_step', ['slug' => $slug]));
        }

        return $this->render('admin/recipe/addStep.html.twig', [
            'form' => $form->createView(),
            'slug' => $slug
        ]);
    }

    public function removeStepAction(Request $request, $slug, $number)
    {
        $command = new RemoveRecipeStepCommand();
        $command->slug = $slug;
        $command->number = (int) $number;

        $targetUrl = $this->generateUrl('admin_recipe_add_step', ['slug' => $slug]);
        
        $request->getSession()->set(self::EXCEPTION_TARGET_URL_KEY, $targetUrl);
        $this->get('my_drinks.command_bus')->handle($command);
        $request->getSession()->remove(self::EXCEPTION_TARGET_URL_KEY);

        return $this->redirect($targetUrl);
    }
    
    public function publishAction($slug)
    {
        $command = new PublishRecipeCommand();
        $command->slug = $slug;

        $this->get('my_drinks.command_bus')->handle($command);

        return $this->redirect($this->generateUrl('admin_recipe_add_step', ['slug' => $slug]));
    }
    
    public function uploadImageAction(Request $request, $slug)
    {
        $command = new UploadRecipeImageCommand();
        $command->slug = $slug;
        $form = $this->createForm(new RecipeImageType(), $command);

        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $command->extension = $form->get('image')->getData()->guessClientExtension();
            $this->get('my_drinks.command_bus')->handle($command);
        }

        return $this->render('admin/recipe/uploadImage.html.twig', [
            'form' => $form->createView(),
            'slug' => $slug
        ]);
    }

    public function removeImageAction($slug)
    {
        $command = new RemoveRecipeImageCommand();
        $command->slug = $slug;
        $this->get('my_drinks.command_bus')->handle($command);

        return $this->redirect($this->generateUrl('admin_recipe_update_description', ['slug' => $slug]));
    }
    
    public function detailsAction($slug)
    {
        $recipe = $this->get('my_drinks.recipes')->findBySlug($slug);
        if (is_null($recipe)) {
            throw new NotFoundHttpException;
        }
        
        return $this->render('admin/recipe/details.html.twig', [
            'recipe' => $recipe,
            'slug' => $slug
        ]);
    }
}