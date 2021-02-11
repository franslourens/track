CREATE TABLE person (
	id serial PRIMARY KEY,
	name VARCHAR ( 50 ) UNIQUE NOT NULL,
	surname VARCHAR ( 50 ) NOT NULL,
	phone VARCHAR ( 255 ) NOT NULL,
	created_at TIMESTAMP NOT NULL,
  updated_at TIMESTAMP 
);

create type color_t as enum('blue', 'red', 'gray', 'black');

CREATE TABLE car (
   id serial PRIMARY KEY,
   make VARCHAR (50) NOT NULL,
   model VARCHAR (50) NOT NULL,
   color color_t,
   licence VARCHAR (50) NOT NULL
);

create type severity_t as enum('most_common', 'less_common', 'serious');

CREATE TABLE symptom (
   id serial PRIMARY KEY,
   description VARCHAR (255) NOT NULL,
   severity severity_t
);


CREATE TABLE person_symptom (
  person_id INT NOT NULL,
  symptom_id INT NOT NULL,
  showing BOOLEAN NULL,
  PRIMARY KEY (person_id, symptom_id)
);

CREATE TABLE person_car (
  person_id INT NOT NULL,
  car_id INT NOT NULL,
  booking_date TIMESTAMP,
  PRIMARY KEY (person_id, car_id)
);