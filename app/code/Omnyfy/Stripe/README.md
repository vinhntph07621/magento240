# Stripe Vendor Payout

## Install Stripe for Magento 2 Module
Stripe Vendor Payout depends on stripe integration module. Link on how to install the module can be found below.
https://stripe.com/docs/plugins/magento/install

#### Steps - 
1. Download the Latest Magento 2 Stripe module. Extract the module in app/code/ directory.
2. Install the stripe vendor module using Composer. // composer require stripe/stripe-php
    * Make sure the version for "stripe/stripe-php": "^6" - this is the version where Magento 2.1.18 supports. 

## Module Configuration

Make sure that Stripe is set to Live/Test depends on the environment that you are working with.

### Configure Stripe for Magento 2 Module
- Config Stripe API key: Store -> Configuration -> Sales -> Payment method -> Stripe -> Configure -> Basic Settings
- Config Stripe API Key under Omnyfy: Store > Configuration → Omnyfy → Stripe API (for subscription)

#### Populate the following field - 
1. Test Publishable API Key
2. Test Secret API Key

Note: This key can be found in the Stripe account under Developers -> API keys (https://dashboard.stripe.com/test/apikeys)

### Configure Stripe Client Id
Go to Stripe Dashboard > Settings > Connect Settings, Copy the Test mode client ID
Set the client ID into following configuration field 
Store -> Configuration -> Omnyfy -> Stripe Config

### Configure Stripe redirction URL
Go to Stripe Dashboard > Settings > Connect Settings
Set Redirect URI: http(s)://{project-host}/{admin_url}/omnyfy_vendor/vendor/index

## Webhook configuration

### Create endpoint receiving events from Stripe account
- Go to stripe > develpers > webhook
- Click Add endpoint and enter the details, select the latest version of API:
- Endpoint URL: http(s)://{project-host}/stripe/webhooks
- Events to send: Select all events for Charges, Sources, Payment Intents and Invoices.
- Scroll to Signing secret, Click to reveal and Copy the key
- Set Signing secret to Stores > Sales > Payment Methods > "Stripe" configure > "Basic Settings" > Test Webhooks Signing Secret

### Add extra endpoints
- /omnyfy_stripe/invoice_payment/succeed/, Event: invoice.payment_succeeded
- /omnyfy_stripe/invoice_payment/failed/, Event: invoice.payment_failed
- /omnyfy_stripe/subscription/created/, Event: customer.subscription.created
- /omnyfy_stripe/subscription/updated/, Event: customer.subscription.updated
- /omnyfy_stripe/subscription/deleted/, Event: customer.subscription.deleted

### Create endpoint receiving events from Connect applications
- Set endpoint url to http(s)://{project-host}/omnyfy_stripe/webhooks
- Click Add endpoint and enter the details, select the latest version of API:
- Select following events to send
    - payout.canceled
    - payout.created
    - payout.failed
    - payout.paid
    - payout.updated
    - account.updated
- Scroll to Signing secret, Click to reveal and Copy the key
- Set Signing secret to Stores > Configuration > Omnyfy > Stripe Config > Connect Account Webhook Signing secret

*Note: Webhooks signing secret: This is an optional key that you can use to verify the origin of webhooks sent to your website. Although the setting is optional, you should set it to ensure that the webhooks you receive are sent by Stripe, rather than a third party. You can retrieve the signing key from the specific webhook that you configured for your website. If you configure the same webhook endpoint for both live and test mode, the signing secret will be different for each mode.*

## How to use Stripe Vendor Payout module

### How vendor creates stripe account
1. Login as Vendor
2. Proceed to Marketplace management -> Vendor profile -> Payout and Banking tab -> Create Stripe account
3. Follow "Set up payments" steps in the Stripe
4. Save configurations

### Maketplace owner payout vendor
1. Login as the MO
2. Proceed to Marketplace management > Pending Payouts
3. Select the checkbox next to the vendor and choose "process payout" in the actions dropdown