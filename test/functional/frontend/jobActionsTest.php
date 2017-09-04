<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');
 
$browser = new JobeetTestFunctional(new sfBrowser());
$browser->loadData();

/**
 * Expired jobs are not listed.
 */
$browser->info('1 - The homepage')->
  get('/')->
  with('request')->begin()->
    isParameter('module', 'job')->
    isParameter('action', 'index')->
  end()->
  with('response')->begin()->
    info('  1.1 - Expired jobs are not listed')->
    checkElement('.jobs td.position:contains("expired")', false)->
  end()
;

/**
 * Solo n puestos se listan para una categoría.
 */
$max = sfConfig::get('app_max_jobs_on_homepage');
 
$browser->info('1 - The homepage')->
  get('/')->
  info(sprintf('  1.2 - Only %s jobs are listed for a category', $max))->
  with('response')->
    checkElement('.category_programming tr', $max)
;

/**
 * Una categoría tiene un enlace a la página de categoría sólo si tiene muchos 
 * puestos de trabajo
 */
$browser->info('1 - The homepage')->
  get('/')->
  info('  1.3 - A category has a link to the category page only if too many jobs')->
  with('response')->begin()->
    checkElement('.category_design .more_jobs', false)->
    checkElement('.category_programming .more_jobs')->
  end()
;

/**
 * Puestos de trabajo están ordenados por fecha.
 */

// $q = Doctrine_Query::create()
//   ->select('j.*')
//   ->from('JobeetJob j')
//   ->leftJoin('j.JobeetCategory c')
//   ->where('c.slug = ?', 'programming')
//   ->andWhere('j.expires_at > ?', date('Y-m-d', time()))
//   ->orderBy('j.created_at DESC');
 
// $job = $q->fetchOne();
 
// $browser->info('1 - The homepage')->
//   get('/')->
//   info('  1.4 - Jobs are sorted by date')->
//   with('response')->begin()->
//     checkElement(sprintf('.category_programming tr:first a[href*="/%d/"]', $job->getId()))->
//   end()
// ;

$browser->info('1 - The homepage')->
  get('/')->
  info('  1.4 - Jobs are sorted by date')->
  with('response')->begin()->
    checkElement(sprintf('.category_programming tr:first a[href*="/%d/"]',
      $browser->getMostRecentProgrammingJob()->getId()))->
  end()
;

/**
 * Cada puesto de trabajo en la página principal es cliqueable.
 *
 * Para probar el vínculo de un puesto en la página de inicio, simularemos un 
 * clic en el texto "Web Developer". Como hay muchos de ellos en la página, 
 * hemos pedido explícitamente al navegador que haga clic en el primero 
 * (array('position' => 1)).
 */

$job = $browser->getMostRecentProgrammingJob();
 
$browser->info('2 - The job page')->
  get('/')->
 
  info('  2.1 - Each job on the homepage is clickable and give detailed information')->
  click('Web Developer', array(), array('position' => 1))->
  with('request')->begin()->
    isParameter('module', 'job')->
    isParameter('action', 'show')->
    isParameter('company_slug', $job->getCompanySlug())->
    isParameter('location_slug', $job->getLocationSlug())->
    isParameter('position_slug', $job->getPositionSlug())->
    isParameter('id', $job->getId())->
  end()->

  info('  2.2 - A non-existent job forwards the user to a 404')->
  get('/job/foo-inc/milano-italy/0/painter')->
  with('response')->isStatusCode(404)
 
  // info('  2.3 - An expired job page forwards the user to a 404')->
  // get(sprintf('/job/sensio-labs/paris-france/%d/web-developer', $browser->getExpiredJob()->getId()))->
  // with('response')->isStatusCode(404)
;