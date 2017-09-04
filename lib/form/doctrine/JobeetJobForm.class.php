<?php

/**
 * JobeetJob form.
 *
 * @package    jobeet
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class JobeetJobForm extends BaseJobeetJobForm
{
  public function configure()
  {
    unset(
      $this['created_at'], $this['updated_at'],
      $this['expires_at'], $this['is_activated'],
      $this['token']
    );

    /**
     * Reemplar el validador por defecto no siempre es la mejor solución, ya que
     * las reglas de validación por defecto inferidas del esquema de la base de 
     * datos se pierden (new sfValidatorString(array('max_length' => 255))). 
     * Es casi siempre mejor para agregar un nuevo validador a uno existente 
     * usar el validador especial sfValidatorAnd:
     */
    // $this->validatorSchema['email'] = new sfValidatorEmail();

    $this->validatorSchema['email'] = new sfValidatorAnd(array(
      $this->validatorSchema['email'],
      new sfValidatorEmail(),
    ));

    $this->widgetSchema['type'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('JobeetJob')->getTypes(),
      'expanded' => true,
    ));

    /**
     * Incluso si piensas que nadie pueda enviar un valor no-válido, un hacker 
     * fácilmente puede pasar por alto las opciones del widget usando herramientas 
     * como curl o la Firefox Web Developer Toolbar. Vamos a cambiar el validador
     * para restringir a las opciones posibles:
     */
    $this->validatorSchema['type'] = new sfValidatorChoice(array(
      'choices' => array_keys(Doctrine_Core::getTable('JobeetJob')->getTypes()),
    ));

    /**
     * Como la columna logo almacenará el nombre del archivo del logotipo 
     * relacionados con el puesto de trabajo, tenemos que cambiar el widget a
     * file input tag
     */
    $this->widgetSchema['logo'] = new sfWidgetFormInputFile(array(
      'label' => 'Company logo',
    ));

    $this->widgetSchema->setLabels(array(
      'category_id'    => 'Category',
      'is_public'      => 'Public?',
      'how_to_apply'   => 'How to apply?',
    ));

    $this->validatorSchema['logo'] = new sfValidatorFile(array(
      'required'   => false,
      'path'       => sfConfig::get('sf_upload_dir').'/jobs',
      'mime_types' => 'web_images',
    ));

    $this->widgetSchema->setHelp('is_public', 
      'Whether the job can also be published on affiliate websites or not.');
  }
}
