<?php
App::uses('AppController', 'Controller');
/**
 * Cron Controller
 *
 * @property User $User
 */
class CronController extends AppController
{

	public $uses = array('User', 'Game');

/**
 * decay method
 *
 * @return void
 */
	public function decay($secret = null)
	{
		$cronSecret = Configure::read('Cron.secret');

		if ($secret === null || $secret !== $cronSecret) {
			throw new MethodNotAllowedException();
		}

		$oneWeekAgo = date ('Y-m-d H:i:s', strtotime ('-1 week', time())); 

		$users = $this->User->find('all', array(
			'conditions' => array(
				'User.modified <=' => $oneWeekAgo
			)
		));
		
		foreach ($users as $user) {
			$this->User->id = $user['User']['id'];
			$currentDecay = $this->User->field('decay');
			$this->User->saveField('decay', $currentDecay + 1);
		}
	
		$this->Game->updateRatings($users);
	}

}