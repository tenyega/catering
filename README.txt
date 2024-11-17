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


CREATED navbar and footer twigs as a components 
and added different routes for starter, main course, dessert, snacks  and its corresponding templates 


ADDED THE BUNDLE 
	composer require symfony/security-core
	 to be able to use it in the cart service 
	 might need another table cart to be able to store the temperory cart details 


ADDED CARTCONTROLLER 
	symfony console make:controller Cart
	/////////////////////////////// THIS TABLE IS NOT ADDED YET /////////////////////////////////////////////////////////////////::
	symfony console make:entity Report
Report
	id int unique not null
	reportDate datetime immutable not null
	totalSalesAmount decimal not null
	totalOrders int not null


FIXTURES 
	composer require orm-fixtures --dev
	composer require fakerphp/faker
	symfony console make:fixtures



Symfony console d:d:c
Symfony console make:migration
Symfony console d:m:m
Symfony console d:f:l




	symfony console make:User
User 
	id int unique not null
	firstName varchar180 not null
	lastName varchar180 not null
	password varchar180  not null
	role varchar180 not null
	paymentStatus varchar180 not null


symfony console make:security:form-login 
		SecurityController with /logout and no phpUnit 

symfony console make:registration-form
		 composer require symfonycasts/verify-email-bundle 

	




ADMIN
	composer req easycorp/easyadmin-bundle
	symfony console make:admin:dashboard	
	symfony console make:admin:crud 
