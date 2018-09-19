#Stripe Tester

Currently, this sandboxy thing lets you play around with Stripe Checkout.

##TODO:

* Handle the response from the backend code. 200 should let the user know that 
their donation succeeded, not-200 should let the user know that their payment 
blew up somewhere.
* Donors should get receipts.
    * I figured out how to grab the email address back from the stripe checkout 
handler response, so now if we just pass it to the backend, stripe will send 
the receipts automatically once you've checked a little checkbox in 
Business Settings -> Customer Emails with an admin account.
