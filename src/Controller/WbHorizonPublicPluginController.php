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
 *        
 */
class WbHorizonPublicPluginController extends ControllerBase
{
	protected static $default_id = 'wb_horizon_com';


	public function configWebforms(Request $request)
	{
		if ($this->ownerAccess(\Drupal::currentUser())->isForbidden()) {
			return $this->forbittenMessage();
		}
		$formStorage = $this->entityTypeManager()->getStorage("webform");

		/**
		 * @var  \Drupal\webform\Entity\Webform[] $webforms
		 */
		$webforms = (\Drupal::moduleHandler()->moduleExists('lesroidelareno')) ?
			$formStorage->loadByProperties(
				[
					"third_party_settings.webform_domain_access.field_domain_access" => \Drupal\lesroidelareno\lesroidelareno::getCurrentDomainId(),
				]
			) : $formStorage->loadMultiple();
		// dd($webforms["contact_de2024Aug19476092"]->get("status"));
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
	public function ConfigureDefaultRDV()
	{
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
	protected function forbittenMessage($message = "Access non authoriser", $context = [])
	{
		$this->getLogger("wb_commerce")->critical($message, $context);
		$this->messenger()->addError($message);
		return [];
	}

	public function ownerAccess(AccountInterface $account)
	{
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
