<?php 
/**
 * Payment Discount Extension for OC 3.0.2.0
 *
 * @package Opencart 3
 * @category Order Total Extension
 * @author Samuel Asor
 * @link https://github.com/sammyskills/opencart-payment-discount
 */
class ControllerExtensionTotalPaymentDiscount extends Controller
{
	
	private $error = array();

	public function index()
	{
		/* Page Title */
		$this->load->language('extension/total/specific_payment_discount');
		$this->document->setTitle($this->language->get('heading_title_plain'));

		$this->load->model('setting/setting');

		/* Process form submission only if form is submitted via POST and
		 * validation is passed
		*/
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_discount', $this->request->post);

			// Store success message in session
			$this->session->data['success'] = $this->language->get('text_success');

			// Redirect URL
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=total', true));
		}
		

		$data = array();

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=total', true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_plain'),
			'href' => $this->url->link('extension/total/payment_discount', 'token=' . $this->session->data['token'], true),
		);

		// Text
		$data['heading_title'] = $this->language->get('heading_title_plain');
		$data['text_edit'] = $this->language->get('text_edit');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_payment_method'] = $this->language->get('entry_payment_method');
		$data['entry_discount'] = $this->language->get('entry_discount');
		$data['entry_title'] = $this->language->get('entry_title');

		$data['help_sort_order'] = $this->language->get('help_sort_order');
		$data['help_name_discount'] = $this->language->get('help_name_discount');

		/*
		 * Get data for installed payment modules
		*/
		$this->load->model('extension/extension');
		$payment_extensions = $this->model_extension_extension->getInstalled('payment');
		$data['payment_methods'] = array();

		foreach ($payment_extensions as $payment_module) {
			if (is_file(DIR_APPLICATION . 'controller/extension/payment/' . $payment_module . '.php')) {
				$this->load->language('extension/payment/' . $payment_module);
				$data['payment_methods'][] = array(
					'name' => $this->language->get('heading_title'),
					'code' => $payment_module,
				);
			}
		}

		/*
		 * Process form fields (get details(name))
		*/
		// Status
		if (isset($this->request->post['payment_discount_status'])) {
			$data['payment_discount_status'] = $this->request->post['payment_discount_status'];
		} else {
			$data['payment_discount_status'] = $this->config->get('payment_discount_status');
		}
		
		// Sort order
		if (isset($this->request->post['payment_discount_sort_order'])) {
			$data['payment_discount_sort_order'] = $this->request->post['payment_discount_sort_order'];
		} else {
			$data['payment_discount_sort_order'] = $this->config->get('payment_discount_sort_order');
		}

		// Payment Type
		if (isset($this->request->post['payment_discount_payment_type'])) {
			$data['payment_discount_payment_type'] = $this->request->post['payment_discount_payment_type'];
		} else {
			$data['payment_discount_payment_type'] = $this->config->get('payment_discount_payment_type');
		}

		// Discount Percentage
		if (isset($this->request->post['payment_discount_percentage'])) {
			$data['payment_discount_percentage'] = $this->request->post['payment_discount_percentage'];
		} else {
			$data['payment_discount_percentage'] = $this->config->get('payment_discount_percentage');
		}

		// Description Text
		if (isset($this->request->post['payment_discount_description'])) {
			$data['payment_discount_description'] = $this->request->post['payment_discount_description'];
		} else {
			$data['payment_discount_description'] = $this->config->get('payment_discount_description');
		}

		// Buttons
		$data['action']['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=total', true);
		$data['action']['save'] = $this->url->link('extension/total/payment_discount', 'token=' . $this->session->data['token'], true);
		

		// Error
		$data['error'] = $this->error;

		// Commons
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// Output
		$this->response->setOutput($this->load->view('extension/total/specific_payment_discount', $data));
	}


	protected function validate() {

		// Permission
		if (!$this->user->hasPermission('modify', 'extension/total/specific_payment_discount')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}