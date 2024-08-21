<?php

namespace Drupal\wb_horizon_public\Controller;

use Symfony\Component\HttpFoundation\Request;
use Drupal\lesroidelareno\lesroidelareno;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Ce controlleur permet de fournir les routes pour l'application de
 * resrvation de creneau ( application de base ).
 *
 * @author stephane
 * @author vysti <ngahabanda@gmail.com>
 *        
 */
class WbHorizonPublicPluginController extends ControllerBase {
	protected static $default_id = 'wb_horizon_com';

	public function submissionsList(Request $request) {
		$webforms = $this->getWebForms();
		$filter = [
			"#type" => "details",
			"#title" => $this->t("Filter"),
			'#open' => true,
			"filter" => $this->formBuilder()->getForm("Drupal\wb_horizon_public\Form\SubmissionFilterForm", $webforms)
		];
		$render = [
			"filter" => $filter,
		];


		/**
		 * Building table
		 */
		$header = [
			'sid' => $this->t('SID'),
			'created' => $this->t('Created'),
			'ip_address' => $this->t('Ip ddress'),
			'web_form' => "Webform",
			'submit_to' => $this->t('Submit to'),
			'actions' => $this->t("Actions")
		];



		//*****************loading submission ids ************************** */
		$query = \Drupal::database()->select("webform_submission", "submt");
		$query->fields("submt", ["sid"]);
		$or = $query->orConditionGroup();
		if ($request->query->get("webform")) {
			$webform_ids = $request->query->get("webform") ?  explode("--", $request->query->get("webform")) : [];
			foreach ($webform_ids as $id) {
				$or->condition("webform_id", $id);
			}
		} else {
			foreach ($webforms as $id => $webform) {
				$or->condition("webform_id", $webform->id());
			}
		}

		$query->condition($or);
		/**
		 *
		 * @var \Drupal\Core\Database\Query\PagerSelectExtender $pager
		 */
		$pager = $query->extend("Drupal\Core\Database\Query\PagerSelectExtender")->limit($request->query->get("limit") ?? 10);
		$query_result = $pager->execute()->fetchAll();
		$submission_ids = array_map(function ($element) {
			return (int)$element->sid;
		}, $query_result);

		// ****************Loading webform submission ***************************//
		/**
		 * @var \Drupal\webform\Entity\WebformSubmission[]
		 */
		$submissions = $this->entityTypeManager()->getStorage("webform_submission")->loadMultiple($submission_ids);

		/**
		 * Building table rows 
		 */
		$rows = [];
		foreach ($submissions as $id => $submission) {
			$webform = $submission->getWebform();
			$actions = [
				'handle' => [
					'title' => $this->t('Edit'),
					'weight' => 0,
					'url' => Url::fromRoute(
						"entity.webform_submission.edit_form",
						[
							'webform' => $webform->id(),
							'webform_submission' => $submission->id()
						],
						[
							'query' => [
								'destination' => $request->getPathInfo()
							]
						]
					)
				],
				'duplicate' => [
					'title' => $this->t('Duplicate'),
					'weight' => 9,
					'url' => Url::fromRoute(
						"entity.webform_submission.duplicate_form",
						[
							'webform' => $webform->id(),
							'webform_submission' => $submission->id()
						],
						[
							'query' => [
								'destination' => $request->getPathInfo()
							]
						]
					)
				],
				'delete' => [
					'title' => $this->t('Delete'),
					'weight' => 10,
					'url' => Url::fromRoute(
						"entity.webform_submission.delete_form",
						[
							'webform' => $webform->id(),
							'webform_submission' => $submission->id()
						],
						[
							'query' => [
								'destination' => $request->getPathInfo()
							]
						]
					)
				]
			];
			$rows[$id] = [
				'sid' => $submission->id(),
				'created' => $submission->getCreatedTime(),
				'ip_address' => $submission->getRemoteAddr(),
				'web_form' => [
					"data" => [
						[
							'#type' => 'link',
							'#title' => $webform->label(),
							'#url' => $webform->toUrl(),
						]
					]
				],
				'submit_to' => $submission->getSourceEntity()->toLink(),
				'actions' =>  [
					"data" => [
						"#type" => "operations",
						"#links" => $actions
					]
				]
			];
			// dd($rows[$id]);
		}
		$render["table"] = [
			'#type' => 'table',
			'#header' => $header,
			'#title' => $this->t("Submissions liste"),
			'#rows' => $rows,
			'#empty' => $this->t("No content found"),
			'#attributes' => [
				'class' => [
					'page-content'
				]
			]
		];
		$render["pager"] = [
			'#type' => "pager"
		];
		return $render;
	}

	/**
	 * Provide the availables webforms
	 * all the webforms if "lesroidelareno" is not  installed and only the webforms availale in the current domain if not
	 * @return \Drupal\webform\Entity\Webform[]
	 */
	protected function getWebForms() {
		$formStorage = $this->entityTypeManager()->getStorage("webform");

		return (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) ?
			$formStorage->loadByProperties(
				[
					"third_party_settings.webform_domain_access.field_domain_access" => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId(),
				]
			) : $formStorage->loadMultiple();
	}


	public function configWebforms(Request $request) {
		if ($this->ownerAccess(\Drupal::currentUser())->isForbidden()) {
			return $this->forbittenMessage();
		}

		/**
		 * @var  \Drupal\webform\Entity\Webform[] $webforms
		 */
		$webforms = $this->getWebForms();
		$header = [
			'name' => $this->t('Name'),
			'statut' => $this->t('Active'),
			'operations' => $this->t('Operations')
		];


		$datas['action_buttons'] = [
			"#type" => "container",
			"add_method" => [
				'#type' => 'link',
				'#title' => $this->t("Add a webform"),
				'#url' => Url::fromRoute('entity.webform.add_form', [], [
					'query' => [
						'destination' => $request->getPathInfo()
					]
				]),
				'#attributes' => [
					"class" => ["button", "button--primary", "button--action"]
				]
			]
		];


		$rows = [];
		foreach ($webforms as $id => $webform) {
			$operations = [
				'handle' => [
					'title' => $this->t('Edit'),
					'weight' => 0,
					'url' => Url::fromRoute(
						"entity.webform.edit_form",
						[
							'webform' => $id
						],
						[
							'query' => [
								'destination' => $request->getPathInfo()
							]
						]
					)
				],
				'duplicate' => [
					'title' => $this->t('Duplicate'),
					'weight' => 9,
					'url' => Url::fromRoute(
						"entity.webform.duplicate_form",
						[
							'webform' => $webform->id()
						],
						[
							'query' => [
								'destination' => $request->getPathInfo()
							]
						]
					)
				],
				'delete' => [
					'title' => $this->t('Delete'),
					'weight' => 10,
					'url' => Url::fromRoute(
						"entity.webform.delete_form",
						[
							'webform' => $webform->id()
						],
						[
							'query' => [
								'destination' => $request->getPathInfo()
							]
						]
					)
				]
			];
			$rows[$id] = [
				'name' => $webform->hasLinkTemplate('canonical') ? [
					'data' => [
						'#type' => 'link',
						'#title' => $webform->label(),
						'#weight' => 10,
						'#url' => $webform->toUrl('canonical')
					]
				] : $webform->label(),
				'statut' => $webform->get("status") ??  $this->t("No"),
				'operations' => [
					'data' => [
						"#type" => "operations",
						"#links" => $operations
					]
				]
			];
		}
		if ($rows) {
			$build['table'] = [
				'#type' => 'table',
				'#header' => $header,
				'#title' => 'Titre de la table',
				'#rows' => $rows,
				'#weight' => 3,
				'#empty' => 'Aucun contenu',
				'#attributes' => [
					'class' => [
						'page-content00'
					]
				]
			];
			$build['pager'] = [
				'#type' => 'pager'
			];

			$datas["table"] = $build;
		}
		return $datas;
	}

	/**
	 * Permet de generer et de configurer RDV par domaine.
	 */
	public function ConfigureDefaultRDV() {
		$entity_type_id = "booking_config_type";
		$id = lesroidelareno::getCurrentPrefixDomain();
		if (!$id) {
			/**
			 * Pour la configuration par defaut.
			 */
			$id = self::$default_id;
		}
		$entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->load($id);
		if (!$entityConfig) {
			$entityConfig = $this->entityTypeManager()->getStorage($entity_type_id)->create([
				'id' => $id,
				'label' => 'Configuration des creneaux',
				'days' => \Drupal\booking_system\DaysSettingsInterface::DAYS
			]);
			$entityConfig->save();
		}

		// dd($entityConfig->toArray());
		// $entityConfig->save();

		$form = $this->entityFormBuilder()->getForm($entityConfig, "edit", [
			'redirect_route' => 'bookingsystem_autoecole.config_resume',
			'booking_config_type_id' => $entityConfig->id()
		]);
		return $form;
	}



	/**
	 * Le but de cette fonction est de notifier l'administrateur l'acces à des
	 * informations senssible.
	 *
	 * @param string $message
	 * @param array $context
	 * @return array
	 */
	protected function forbittenMessage($message = "Access non authoriser", $context = []) {
		$this->getLogger("wb_commerce")->critical($message, $context);
		$this->messenger()->addError($message);
		return [];
	}

	public function ownerAccess(AccountInterface $account) {
		if (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) {
			if (\Drupal\lesroidelareno\lesroidelareno::FindUserAuthorDomain() || \Drupal\lesroidelareno\lesroidelareno::isAdministrator()) {
				return AccessResult::allowed();
			}
		} elseif (in_array('administrator', $account->getRoles())) {
			return AccessResult::allowed();
		}
		return AccessResult::forbidden();
	}
}
