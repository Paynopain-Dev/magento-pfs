# Magento 2 Module Paynopain Payments

- [Magento 2 Module Paynopain Payments](#magento-2-module-paynopain-payments)
  - [Main Functionalities](#main-functionalities)
  - [Installation](#installation)
  - [Configuration](#configuration)

## Main Functionalities
Paylands Paynopain payment methods extension for Magento 2.

## Installation

 - Unzip the zip file in `app/code/`
 - Enable the module by running `php bin/magento module:enable Paynopain_Payments`
 - Apply database updates by running `php bin/magento setup:upgrade`
 - Deploy static files by running `php bin/magento setup:static-content:deploy [LANGUAGES]`
 - Flush the cache by running `php bin/magento cache:flush`

## Configuration
 - **Documentations & support**
    - magento@paylands.com
    - https://docs.paylands.com/docs/
 - **Integration**
    > Environment: Sandbox for test, real for production.
    > API key: Provided in your Paylands panel.
    > Signature: Provided in your Paylands panel.
    > Service: Provided in your Paylands panel.
    > Enable 3DS: Option to apply the PSD2 directive
    > Integration type: Redirect or iframe.
        - For redirect the customer will be redirected to Paynopain website to continue the payment, after that the customer will be redirected to the online store.
        - For iframe the customer will remain in the online store and the payment will be trough an iframe in the same domain of the online store.
    > Checkout UUID: Provided in you Paylands panel. Will use an specific checkout layout configured in your Paynopain panel. This can be use to change which payment method are available, change colors, logo, etc.
 - **General**
    > Active: Enables or disables the functionality.
    > Title: The text that will show up in the checkout flow.
    > Description: Short text for a description. Will show up in the checkout flow.
    > Applicable to countries: Only show for all or specific countries.
    > Appearance order: Order of appearance in the payment methods list. Can overlap with other payment methods.
 - **Order settings**
    > Payment action: Authorize or Authorize & Capture the payment. Note: In case of authorize only, the capture will be done in a cron job.
   > New order status for orders: After payment is confirmed, the order will have this status.
   > Capture order on status: Only appears with payment action is "Authorize". This means that only order paid with Paynopain method and with this status, will be capture on next cron job execution.
   > Auto generate invoice: When confirmation of payment, a Magento invoice will be created.
   > Message error: When there is a problem in the Paynopain flow, or another capture errores, the user will be redirected to the cart and will be shown this message. The token "{{AVAILABLE_METHODS}}" will be used to show another Paylands payment methods.
 - **Other settings**
    >Debug logs: Enable logs in file system. Magento root path/var/log/paylands_paynopain/debug.log.