<?php

namespace App\Presenters;

use Nette,  
    Nette\Utils\Html;
use App\Model;
use Grido,
    Nette\Application\UI\Form;
    

class OsobaPresenter extends BasePresenter
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    
    public function renderDefault() {
        
    }
    public function renderDelete($id=0){
        
    }
    public function actionEdit($id){
	$osoba = $this->database->table('osoba')->get($id);
	if (!$osoba){
	    $this->error('Osoba nebola nájdená!!!');
	}
	
	$this['osobaForm']->setDefaults($osoba);
    }

    protected function createComponentOsobaGrid($name)
{
    $grid = new Grido\Grid($this, $name);
    $grid->setModel($this->database->table('osoba'));
    
    $grid->addColumnText('priezvisko', 'Priezvisko')->setFilterText();
    $grid->addColumnText('meno', 'Meno');
    $grid->addColumnText('cisloCrv', 'CRV');
    $grid->addColumnText('vcelstvaJar', 'Pocet jar');
    $grid->addColumnText('vcelstvaJesen', 'pocet jesen');
    $grid->addActionHref('edit', 'Edituj')->setIcon('pencil');
    $grid->addActionHref('delete', 'Zmaž')->setIcon('trash');
}

protected function createComponentDeleteForm() {
    $form = new Form;
    $form->addSubmit('delete','Zmaž')->onClick[] = array($this, 'deleteFormDelete');
    $form->addSubmit('cancel','Storno')->onClick[] = array($this,'deleteFormCancel');
    $form->addProtection();
    return $form;
    
}
public function deleteFormDelete(){
    //$ida = $this->getParameter('id');
    //dump($ida);
    $this->flashMessage('Osoba úspešne zmazaná');
    $this->database->table('osoba')->where('id', $this->getParameter('id'))->delete();
    $this->redirect('default');
}

public function deleteFormCancel(){
    $this->redirect('default');   
}

protected function createComponentOsobaForm() {
    $form = new Form;
    $form->addText('priezvisko', 'Priezvisko');
    $form->addText('meno', 'Meno');
    $form->addText('ulica', 'Ulica');
    $form->addText('mesto', 'Mesto');
    $form->addText('psc', 'PSČ');
    $form->addText('cisloCrv', 'Číslo CRV');
    $form->addText('vcelstvaJar','Včelstvá jar');
    $form->addText('vcelstvaJesen','Včelstvá jeseň');
    $form->addSubmit('send', 'Uložiť')->onClick[] =array($this,'osobaFormSucceeded') ;
    $form->addSubmit('cancel','Storno')->onClick[] = array($this,'osobaFormCancel');
    return $form;
    
}

public function osobaFormSucceeded($form){
    
    $values =	$form->getForm()->getValues();
    $osobaId = $this->getParameter('id');
    if ($osobaId){
	$osoba = $this->database->table('osoba')->get($osobaId);
	$osoba->update($values);
    }else{
    $osoba=$this->database->table('osoba')->insert($values);
    }
    $this->flashMessage('Osoba úspešne uložená','success');
    $this->redirect('default');
    
}

public function osobaFormCancel(){
    $this->redirect('default');   
}

}