<?php

/**
 * job actions.
 *
 * @package    jobeet
 * @subpackage job
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class jobActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    //La lógica para obtener los puestos de trabajo activos está ahora 
    //en el modelo, donde pertenerce
    // $q = Doctrine_Query::create()
    //   ->from('JobeetJob j')
    //   ->where('j.expires_at > ?', date('Y-m-d h:i:s', time()));
    // $this->jobeet_jobs = $q->execute();
    
    // $this->jobeet_jobs = Doctrine_Core::getTable('JobeetJob')->getActiveJobs();
    
    //Hasta ahora, no teníamos la categoría en cuenta. De los requisitos, 
    //la página de inicio debe mostrar los puestos de trabajo por categoría. 
    //Primero, necesitamos obtener todas las categorías con al menos un puesto 
    //de trabajo activo.
    $this->categories = Doctrine_Core::getTable('JobeetCategory')->getWithJobs();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->job = Doctrine_Core::getTable('JobeetJob')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->job);
  }

  public function executeNew(sfWebRequest $request)
  {
    // $this->form = new JobeetJobForm();
    
    /**
     * Tambien puedes definir los valores por defecto para la creación. Una forma
     * es declarar los valores en el esquema de base de datos. Otra es pasar un 
     * pre-modificado objeto Job al constructor de form.
     */
    $job = new JobeetJob();
    $job->setType('full-time');
   
    $this->form = new JobeetJobForm($job);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new JobeetJobForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    // $this->forward404Unless($jobeet_job = Doctrine_Core::getTable('JobeetJob')
    //   ->find(array($request->getParameter('id'))), 
    //   sprintf('Object jobeet_job does not exist (%s).', $request->getParameter('id')));
    // $this->form = new JobeetJobForm($jobeet_job);

    $this->form = new JobeetJobForm($this->getRoute()->getObject());
  }

  public function executeUpdate(sfWebRequest $request)
  {
    // $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    // $this->forward404Unless($jobeet_job = Doctrine_Core::getTable('JobeetJob')->
    //   find(array($request->getParameter('id'))), 
    //   sprintf('Object jobeet_job does not exist (%s).', $request->getParameter('id')));
    // $this->form = new JobeetJobForm($jobeet_job);

    $this->form = new JobeetJobForm($this->getRoute()->getObject());

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    // $this->forward404Unless($jobeet_job = Doctrine_Core::getTable('JobeetJob')->
    //   find(array($request->getParameter('id'))), 
    //   sprintf('Object jobeet_job does not exist (%s).', $request->getParameter('id')));
    
    $job = $this->getRoute()->getObject();

    $jobeet_job->delete();

    // $this->redirect('job/index');
    $this->redirect($this->generateUrl('job'));
  }

  public function executePublish(sfWebRequest $request)
  {
    $request->checkCSRFProtection();
   
    $job = $this->getRoute()->getObject();
    $job->publish();
   
    $this->getUser()->setFlash('notice', sprintf('Your job is now online for %s days.', sfConfig::get('app_active_days')));
   
    $this->redirect($this->generateUrl('job_show_user', $job));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind(
      $request->getParameter($form->getName()), 
      $request->getFiles($form->getName())
    );

    if ($form->isValid())
    {
      $job = $form->save();

      // $this->redirect('job/edit?id='.$job->getId());
      $this->redirect($this->generateUrl('job_show', $job));
    }
  }
}
