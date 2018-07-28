# newsletter2go
VanHack Developer Test

The app was developed applying MVC and its workflow works like the diagram below:

						                  +----> HTML
					                  	|	      |
			User <----> Control <---|     Model
						                  |       |
						                  +----> JSON

An user always makes a request to the Control. The Control checks the URL to choose an executor, an entity and an action. 

Executors can be a JSON or HTML. JSON and HTML executors access the database via Model executor. 

There are 3 entities: SpecialOffer, Recipient and Voucher. Each entity has a Model, a HTML and a JSON executor.

Besides this, each combination of executor + entity can do some actions.

	- SpecialOffer: add, find_all, match, etc;

	- Recipient: add, find_all, match, etc;

	- Voucher: add, count_vouchers, find_all, find_offers, find_valid_by_recipient, match, validate, etc.

The app's URL is https://localhost/newsletter2go/.

To apply the API to some code, use a JSON request via HTTP. Some AJAX examples:

	- Create a voucher
		- url: 'https://localhost/newsletter2go/JSON/VOUCHER/ADD'
		- type: 'post'
		- data: { params: [ null, int offer_id, int recipient_id, date expiration_date ] }
		- dataType: 'json'
		- success: function(result) { /* bool result.fail, string result.message, string result.url */ }

	- List valid vouchers:
		- url: 'https://localhost/newsletter2go/JSON/VOUCHER/LIST'
		- type: 'post'
		- data: { params: int recipient_id }
		- dataType: 'json'
		- success: function(result) { /* result[ ] = { recipient, email, offer, code } */ }

	- Validate the voucher:
		- url: 'https://localhost/newsletter2go/JSON/VOUCHER/VALIDATE'
		- type: 'post'
		- data: { params: [ string code, string email ] }
		- dataType: 'json'
		- success: function(result) { /* bool result.fail, string result.message, string result.url, float result.data */ }
