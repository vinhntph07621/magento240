# Omnyfy MCM
## Requirements
1. Please ensure that Guest Web API is enabled (screenshot)
```
Stores > Settings > Configuration > Services > Magento Web API > Web API Security.
```
![Postman](readme_images/webapi_security.png)

## API
API current quote transaction fee:
```
PARAMS: {quoteId}
POST http://{{marketplaceurl}}/rest/V1/mcm/transactionfee/quote/{quoteId}
```

![Transaction Fee](readme_images/mcm_transaction_fee.png)
![Postman](readme_images/mcm_trans_postman.png)


Request Body (will return an integer value that is the current quote/cart transaction fee in database):
```
6.1630
```
