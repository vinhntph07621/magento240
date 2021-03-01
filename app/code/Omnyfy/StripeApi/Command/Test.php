<?php
/**
 * Project: Strip API
 * User: jing
 * Date: 2019-07-17
 * Time: 14:50
 */
namespace Omnyfy\StripeApi\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class Test extends Command
{
    protected $appState;

    protected $helper;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\StripeApi\Helper\Data $helper,
        $name = null
    )
    {
        $this->appState = $state;
        $this->helper = $helper;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('omnyfy:stripe_api:test');
        $this->setDescription('Test stripe API');
        $this->addArgument('c', InputArgument::REQUIRED, 'Command');
        $this->addArgument('p', InputArgument::OPTIONAL, 'Parameter');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try{
            $cmd = $input->getArgument('c');
            $param = $input->getArgument('p');
            $param = empty($param) ? false : $param;

            $output->writeln("Run " . $cmd . ' ' . $param);

            $result = [];
            switch($cmd) {
                case 'search_customer':
                    $email = $param;
                    $result = $this->helper->searchCustomer($email);
                    break;
                case 'create_customer':
                    $email = $param;
                    $result = $this->helper->createCustomer($email);
                    break;
                case 'retrieve_customer':
                    $id = $param;
                    $result = $this->helper->retrieveCustomer($id);
                    break;
                case 'list_products':
                    $limit = $param;
                    $result = $this->helper->listProducts($limit);
                    break;
                case 'create_product':
                    $name = $param;
                    $result = $this->helper->createProduct($name);
                    break;
                case 'retrieve_product':
                    $id = $param;
                    $result = $this->helper->retrieveProduct($id);
                    break;
                case 'search_plan':
                    $productId = $param;
                    $result = $this->helper->searchPlan($productId);
                    break;
                case 'create_plan':
                    $productId = $param;
                    $result = $this->helper->createPlan($this->generatePlan($productId));
                    break;
                case 'retrieve_plan':
                    $id = $param;
                    $result = $this->helper->retrievePlan($id);
                    break;
                case 'search_subscription':
                    list($customerId, $planId) = explode(" ", $param);
                    $result = $this->helper->searchSubscription($customerId, $planId);
                    break;
                case 'create_subscription':
                    list($customerId, $planId) = explode(" ", $param);
                    $result = $this->helper->createSubscription($customerId, $planId);
                    break;
                case 'retrieve_subscription':
                    $id = $param;
                    $result = $this->helper->retrieveSubscription($id);
                    break;
                case 'cancel_subscription':
                    $id = $param;
                    $result = $this->helper->cancelSubscription($id);
                    break;
                case 'delete_subscription':
                    $id = $param;
                    $result = $this->helper->deleteSubscription($id);
                    break;
                case 'update_subscription':
                    list($id, $field, $value) = explode(" ", $param);
                    $result = $this->helper->updateSubscription($id, [$field => $value]);
                    break;
                case 'retrieve_card':
                    list($customerId, $cardId) = explode(" ", $param);
                    $result = $this->helper->retrieveCard($customerId, $cardId);
                    break;
                case 'list_cards':
                    $id = $param;
                    $result = $this->helper->listCards($id);
                    break;
                case 'create_card':
                    list($customerId, $token) = explode(" ", $param);
                    $result = $this->helper->createCard($customerId, $token);
                    break;
                case 'list_webhooks':
                    $limit = $param;
                    $result = $this->helper->listWebhooks($limit);
                    break;
                case 'create_webhook':
                    list($url, $event) = explode(" ", $param);
                    $result = $this->helper->createWebhook($url, $event);
                    break;
                case 'update_webhook':
                    list($webhookId, $url, $event) = explode(" ", $param);
                    $result = $this->helper->updateWebhook($webhookId, $url, $event);
                    break;
            }

            $output->writeln(json_encode($result, true));
        }
        catch (\Exception $e)
        {

        }
    }

    protected function generatePlan($productId) {
        return [
            'currency' => 'aud',
            'interval' => 'month',
            'product' => $productId,
            'amount' => 10
        ];
    }
}