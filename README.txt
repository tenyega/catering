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




	/////////////////////////////// THIS TABLE IS NOT ADDED YET /////////////////////////////////////////////////////////////////::




symfony console make:security:form-login 
		SecurityController with /logout and no phpUnit 

symfony console make:registration-form
		 composer require symfonycasts/verify-email-bundle 

	


ADMIN
	composer req easycorp/easyadmin-bundle
	symfony console make:admin:dashboard	
	symfony console make:admin:crud 
