<?php
declare(strict_types=1);

namespace App\Controller\Admin;
use App\Controller\AppController;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {   
        $this->paginate = ['limit'=>200,
                'contain' => ['Categories', 'Measurements'],
        ];

        //Si se realiza el filtrado
        if(isset($_POST['category_id'])){
            $category_id = $_POST['category_id'];
            //Almacena los productos si son del mismo category_id
            $query = $this->Products->find('all', [
                'conditions' => ['category_id LIKE' => $category_id]
            ]);

            $products = $this->paginate($query);
            
        }
        //Si no se realiza el filtrado
        else{
            $products = $this->paginate($this->Products);
        }

        $categories = $this->Products->Categories->find('list', ['limit' => 200]);
        $this->set(compact('products', 'categories'));
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Measurements', 'Purchases'],
        ]);

        $this->set(compact('product'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $product = $this->Products->newEmptyEntity();
        if ($this->request->is('post')) {
            $product = $this->Products->patchEntity($product, $this->request->getData());
            if ($this->Products->save($product)) {
                $this->Flash->success(__('El producto has sido guardado.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('El producto no pudo ser guardado, Intente de nuevo.'));
        }
        $categories = $this->Products->Categories->find('list', ['limit' => 200]);
        $measurements = $this->Products->Measurements->find('list', ['limit' => 200]);
        $purchases = $this->Products->Purchases->find('list', ['limit' => 200]);
        $this->set(compact('product', 'categories', 'measurements', 'purchases'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['Purchases'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $product = $this->Products->patchEntity($product, $this->request->getData());
            if ($this->Products->save($product)) {
                $this->Flash->success(__('El producto has sido guardado.'));

                return $this->redirect(['action' => 'index']);
            }
            
            $this->Flash->error(__('El producto no pudo ser guardado, Intente de nuevo.'));
        }
        $categories = $this->Products->Categories->find('list', ['limit' => 200]);
        $measurements = $this->Products->Measurements->find('list', ['limit' => 200]);
        $purchases = $this->Products->Purchases->find('list', ['limit' => 200]);
        $this->set(compact('product', 'categories', 'measurements', 'purchases'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('El producto has sido eliminado'));
        } else {
            $this->Flash->error(__('El producto no pudo ser eliminado, intente de nuevo.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
