<?php

namespace App\Controller;

class ArticlesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
    }

    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');
        // Les actions 'add' et 'tags' sont toujours autorisés pour les utilisateur
        // authentifiés sur l'application
        if (in_array($action, ['add', 'tags'])) {
            return true;
        }

        // Toutes les autres actions nécessitent un slug
        $slug = $this->request->getParam('pass.0');
        if (!$slug) {
            return false;
        }

        // On vérifie que l'article appartient à l'utilisateur connecté
        $article = $this->Articles->findBySlug($slug)->first();

        return $article->user_id === $user['id'];
    }

    public function index()
    {
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    public function view($slug)
    {
        $article = $this->Articles->findBySlug($slug)->contain(['Tags'])->firstOrFail();
        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
    
            $article->user_id = $this->Auth->user('id');
    
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Votre article a été sauvegardé.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Impossible d\'ajouter votre article.'));
        }
        $tags = $this->Articles->Tags->find('list');

        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    public function edit($slug)
    {
    //     $article = $this->Articles->findBySlug($slug)->contain(['Tags'])->firstOrFail();
    //     if ($this->request->is(['post', 'put'])) {
    //         $this->Articles->patchEntity($article, $this->request->getData());
    //         if ($this->Articles->save($article)) {
    //             $this->Flash->success(__('Votre article a été modifié.'));
    //             return $this->redirect(['action' => 'index']);
    //         }
    //         $this->Flash->error(__('Impossible de mettre à jour votre article.'));
    //     }
    
    //     // Récupère une liste des tags.
    //     $tags = $this->Articles->Tags->find('list');
    
    //     // Passe les tags au context de la view
    //     $this->set('tags', $tags);
    //     $this->set('article', $article);

        $article = $this->Articles
        ->findBySlug($slug)
        ->contain('Tags') // Charge les tags associés
        ->firstOrFail();

        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                // Ajouté : Empêche la modification du user_id.
                'accessibleFields' => ['user_id' => false]
            ]);
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Votre article a été modifié.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Impossible de mettre à jour l\'article.'));
        }
        $tags = $this->Articles->Tags->find('list');

        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function tags(...$tags)
    {
        // Use the ArticlesTable to find tagged articles.
        $articles = $this->Articles->find('tagged', [
            'tags' => $tags
        ]);

        // Pass variables into the view template context.
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }
}