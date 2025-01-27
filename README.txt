CREATING NEW SYMFONY PROJECT
 	Symfony new catering --webapp

INSTALLATION TAILWINDCSS
	composer require symfonycasts/tailwind-bundle

	php bin/console tailwind:init

	php bin/console tailwind:build --watch

CONTROLLER 
	symfony console make:controller Home


ENTITIES

Customer
	symfony console make:user Customer
	email unique
	password hashed and Role added automatically


	symfony console make:entity Customer
	id int unique not null
	firstName varchar120 not null	
	lastName varchar120 not null
	phone varchar120 not null
	address varchar255 not null


Employee
	Symfony console make:user Employee
	email and hashed password yes and Role added automatically


MenuItem 
	symfony console make:entity MenuItem
	name varchar255 not null
	description text nullable
	price decimal 5,2 not  null
	category varchar120 not null
	isAvailable boolean not null

OrderItem 
	symfony console make:entity OrderItem
	quantity integer not null
	itemPrice decimal 5,2 not null
	MenuItem M21 with MenuItem(OrderItem.MenuItem nullable: NO, menuItem->getOrderItems(): YES, New fieldName in MenuItems: orderItems, orphanRemoval: NO)
	specialRequest text nullable
	///ORDER: M21 with ORDER not added yet 


Order
	symfony console make:entity Order
	id int unique not null
	orderDate  datetime_immutable not null
	totalAmount decimal 10,2 not null
	customer M21 with Custormer not null ( Order.customer nullable: NO, $customer->getOrders(): YES, New field in Customer:orders, orphanRemoval: NO)
	employee M21 with Employee not null( Order.employee nullable: NO,  $employee->getOrders(): YES, New field in employee: orders, orphanRemoval: NO)
	paymentStatus: varchar255 not null
	orderStatus varchar120 not null

	

UPDATE OF OrderItem
	symfony console make:entity OrderItem
	orders M21 with Order (OrderItem.orders nullable: NO,$order->getOrderItems(): YES, New field in Order table: orderItems, orphanRemoval:NO )


Payment
	symfony console make:entity Payment
	amount decimal 10,2 not null
	paymentDate datetime_immutable not null
	orderId 121 with Order (Payment.orderId nullable: NO, $order->getPayment(): YES, New field in Order: payment, )
	paymentMethod varchar255 not null
	createdAt datetime_immutable not null
	updatedAt datetime_immutable not null


ADDED IMAGE TO THE MENUITEM 
	symfony console make:entity MenuItem
	img varchar120 not null



FIXTURES 
	composer require orm-fixtures --dev
	composer require fakerphp/faker
	symfony console make:fixtures



Symfony console d:d:c
Symfony console make:migration
Symfony console d:m:m
Symfony console d:f:l





FIXTURES 
	composer require orm-fixtures --dev
	composer require fakerphp/faker
	symfony console make:fixtures



Symfony console d:d:c
Symfony console make:migration
Symfony console d:m:m
Symfony console d:f:l




CREATED navbar and footer twigs as a components 
and added different routes for starter, main course, dessert, snacks  and its corresponding templates 


ADDED THE BUNDLE 
	composer require symfony/security-core
	 to be able to use it in the cart service 
	 might need another table cart to be able to store the temperory cart details 


ADDED CARTCONTROLLER 
	symfony console make:controller Cart

	Added the cart items in the sessions and displayed it in a seperate route cart_show at this stage m not using the CartService 
	total Quantity and total amount is calculated at cartcontroller and then only its passed to the twig files 
	Its the index.html.twig under the cart which displays the actual cart items ( until this stage the values of the cart are not affected inside the database at all )


INCLUDING PAYMENT 
	symfony console make: controller Payment
	added PaymentService  to the service folder and STRIPE KEYS INSIDE THE env.local and also as a parameter inside the service.yaml 

	INTEGRATED STRIPE 
	 composer require stripe/stripe-php


CRUD MENUITEM
symfony console make:controller MenuItem 

symfony console make:form MenuItem

CREATED TWIG EXTENSION UNDER Twig folder called FileExistsExtension.php which checks if the image exist on the server or not 
and then register the twig extension inside the services.yaml 
	services:
  	# default configuration for services in *this* file
  		App\Service\PaymentService: ~
  		App\Twig\FileExistsExtension:
        	tags: [ 'twig.extension' ]
	now that the extension is registered in the service.yaml, u can use it like 
		<img class="object-cover w-full h-48" src="{{ file_exists('img/' ~ menuItem.name ~ '.jpg') ? asset('img/' ~ menuItem.name ~ '.jpg') : asset('img/default.jpg') }}" alt="Menu Image">


CRUD Customer 
symfony console make:controller Customer
symfony console make:form Customer


CRUD Employee
symfony console make:controller Employee
symfony console make:form Employee
ADMIN RIGHTS 
	- CRUD MENUITEM
	- CRUD Custormer
	- CRUD Employee
	


REPORT TABLE 
	symfony console make:entity Report
Report 
Report 
	id int unique not null
	reportDate datetime immutable not null
	totalSalesAmount decimal not null(precision 10, scale 2)
	totalSalesAmount decimal not null(precision 10, scale 2)
	totalOrders int not null


DROPPED THE DATABASE VIA PHPMYADMIN
	 symfony console d:d:c 
	 symfony console d:m:m
	symfony console d:f:l  

OneToMany relation of order table with WeeklyReport
	symfony console make:entity order
	report related(ManyToOne) to table Report 
	Order.reportId nullable? yes
	report->getOrders() ? yes 
	orders column inside report ? yes 
	
	
	symfony console make:migration
	 symfony console d:m:m


CREATED A NEW TWIG EXTENSION TO CLEAR THE SESSION AT TWIG LEVEL (SessionExtension.php ) 
CHANGES in Service.yaml under service
		   App\Twig\SessionExtension:
        arguments:
            $session: '@session'
        tags: ['twig.extension']

	USAGE {% do set_session('cart', []) %} inside twig file 


CRUD ORDER
	symfony console make:controller Order    // kept report inside the order as a comment coz i was not able to get this at the front side. 


Logins 
	symfony console make:security:form-login 
	SecurityController with /logout and no phpUnit 



	email unique
	password hashed and Role added automatically

USER 
	symfony console make:user User
	id int unique not null
	firstName varchar120 not null	
	lastName varchar120 not null
	phone varchar120 not null
	address varchar255 not null


Created UserController 
	symfony console make:controller User

registration
	symfony console make:registration-form
			 By default, users are required to be authenticated when they click the verification link that is emailed to them.
 This prevents the user from registering on their laptop, then clicking the link on their phone, without
 having to log in. To allow multi device email verification, we can embed a user id in the verification link.

	 Would you like to include the user id in the verification link to allow anonymous email verification? (yes/no) [no]: YES
	 
 	What email address will be used to send registration confirmations? (e.g. mailer@your-domain.com):
 				> mdolma@ymail.com
	 What "name" should be associated with that email address? (e.g. Acme Mail Bot):
				 > Catering
	 Do you want to automatically authenticate the user after registration? (yes/no) [yes]: No
	 After Registration redirect the user to Login Page 
	  Do you want to generate PHPUnit tests? [Experimental] (yes/no) [no]: No

Email Bundle required for the registration form. 
	composer require symfonycasts/verify-email-bundle
	
	Needed 
		symfony console make:migration to generate a migration for the newly added User::isVerified property.
		symfony console d:m:m   // needed to reflect the changes stored in the migration to our database; 



	

symfony console make:registration-form
		 composer require symfonycasts/verify-email-bundle 


Added sending email to the user ones the payment is successful. 

404 page added the template under home and created a route inside the homeController with a name app_404. Along with which changed inside the routes.yaml to make this a default route when no routes are found 
app_default:
    path: /{any}
    controller: App\Controller\HomeController::pageNotFound
    requirements:
        any: .*


Added changing pwd option: in which i have a remarque that we can do the comparison of the hashed pwd directly. we can use the $this->hasher->isPasswordValid($user, $currentPassword) in which the $user here is the connected user  and the currentPassword is the entered currentPassword in the form 

	/////////////////////////////// THIS IS NOT ADDED YET /////////////////////////////////////////////////////////////////::


composer require google/recaptcha-bundle



To launch the mailer service 
symfony console messenger:consume async -vv

1-------------------------------- Validations to the Change Password is done -------------------------------

1. first of all when you call the ChangePasswordType form, pass the user as an option to the form.
		//passing the current connected user to the ChangePasswordType form to compare the password which the user has entered and the user which is connected 
        $form = $this->createForm(ChangePasswordType::class, [
            'user' => $user
        ]);

1. inside the callback function under the constraints, where you can access the options directly inside the call back function, where the  buildForm(FormBuilderInterface $builder, array $options): automatically accepts the options. 
     
	new Callback(function ($value, ExecutionContextInterface $context) use ($options) {

                        $user = $options['data']['user']; // Get the user passed as an option
                        if (!$user || !password_verify($value, $user->getPassword())) {// here the password_verify is the function that actually does the verification as the values are same and also to check if the user really is an object . 
						
                            $context->buildViolation('The current password is incorrect.')
                                ->addViolation();
                        }
                    }),  

2. check the new password entered contains atleast 6 char. 
	using Assrt\Length() 
3. check the confirm password is same as that of new password entered by the user. 
	Again using a callback function. 
	where the form is collected using the context->getRoot() function. 
	where will get the newPassword field of the form and compare it with the value of the confirmPassord itself 
4. At the twig side of the change password added 
	{% if form.currentPassword.vars.errors|length > 0 %}
					<div class="mt-1 text-sm text-red-500 errorMsg">
						{% for error in form.currentPassword.vars.errors %}
							{{ error.message }}
						{% endfor %}
					</div>
				{% endif %} 
	To show the error message in red color. 
NEED TO DO THE VALIDATIONS 








Translator for the web application
	composer require symfony/translation


Inside config\packages\translation.yaml 
	change the  default_locale: en to english as ur application is in english 