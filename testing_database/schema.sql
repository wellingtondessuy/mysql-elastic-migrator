CREATE TABLE customers (
	`id` int(10) AUTO_INCREMENT NOT NULL,
	`name` varchar(191) NOT NULL,
	`group` varchar(50) NOT NULL,
	`created_at` TIMESTAMP NULL,
	`updated_at` TIMESTAMP NULL,
	`deleted_at` TIMESTAMP NULL,
	CONSTRAINT customers_pk PRIMARY KEY (`id`)
)
ENGINE=InnoDB;

CREATE TABLE product_categories (
	`id` int(10) auto_increment NOT NULL,
	`title` varchar(191) NOT NULL,
	`created_at` TIMESTAMP NULL,
	`updated_at` TIMESTAMP NULL,
	`deleted_at` TIMESTAMP NULL,
	CONSTRAINT product_categories_pk PRIMARY KEY (`id`)
)
ENGINE=InnoDB;

CREATE TABLE products (
	`id` int(10) auto_increment NOT NULL,
	`name` varchar(191) NOT NULL,
	`category_id` int(10) NOT NULL,
	`created_at` TIMESTAMP NULL,
	`updated_at` TIMESTAMP NULL,
	`deleted_at` TIMESTAMP NULL,
	CONSTRAINT products_pk PRIMARY KEY (`id`)
)
ENGINE=InnoDB;

CREATE TABLE sales (
	`id` int(10) auto_increment NOT NULL,
	`customer_id` int(10) NOT NULL,
	`date` DATE NOT NULL,
	`created_at` TIMESTAMP NULL,
	`updated_at` TIMESTAMP NULL,
	`deleted_at` TIMESTAMP NULL,
	CONSTRAINT sales_pk PRIMARY KEY (`id`)
)
ENGINE=InnoDB;

CREATE TABLE sale_products (
	id int(10) auto_increment NOT NULL,
	`sale_id` int(10) NOT NULL,
	`product_id` int(10) NOT NULL,
	`quantity` int(10) NOT NULL,
	`unit_value` DECIMAL(18,2) NOT NULL,
	`created_at` TIMESTAMP NULL,
	`updated_at` TIMESTAMP NULL,
	`deleted_at` TIMESTAMP NULL,
	CONSTRAINT sale_products_pk PRIMARY KEY (`id`)
)
ENGINE=InnoDB;