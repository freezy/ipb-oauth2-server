<?php

$TABLE[] = 'CREATE TABLE oauth_clients (
	client_id VARCHAR(80) NOT NULL,
	client_secret VARCHAR(80) NOT NULL,
	client_name VARCHAR(255) NOT NULL,
	homepage_uri VARCHAR(255),
	homepage_description VARCHAR(2000),
	homepage_logo VARCHAR(2000),
	redirect_uri VARCHAR(2000),
	grant_types VARCHAR(80),
	scope VARCHAR(100),
	member_id MEDIUMINT(8) NOT NULL,
	CONSTRAINT pk_client_id PRIMARY KEY (client_id)
);';

$TABLE[] = 'CREATE TABLE oauth_access_tokens (
	access_token VARCHAR(40) NOT NULL,
	client_id VARCHAR(80) NOT NULL,
	member_id MEDIUMINT(8) NOT NULL,
	expires TIMESTAMP NOT NULL,
	scope VARCHAR(2000),
	CONSTRAINT pk_access_token PRIMARY KEY (access_token)
);';

$TABLE[] = 'CREATE TABLE oauth_authorization_codes (
	authorization_code VARCHAR(40) NOT NULL,
	client_id VARCHAR(80) NOT NULL,
	member_id MEDIUMINT(8) NOT NULL,
	redirect_uri VARCHAR(2000),
	expires TIMESTAMP NOT NULL,
	scope VARCHAR(2000),
	CONSTRAINT pk_auth_code PRIMARY KEY (authorization_code)
);';

$TABLE[] = 'CREATE TABLE oauth_refresh_tokens (
	refresh_token VARCHAR(40) NOT NULL,
	client_id VARCHAR(80) NOT NULL,
	member_id MEDIUMINT(8) NOT NULL,
	expires TIMESTAMP NOT NULL,
	scope VARCHAR(2000),
	CONSTRAINT pk_refresh_token PRIMARY KEY (refresh_token)
);';

$TABLE[] = 'CREATE TABLE oauth_members (
	client_id VARCHAR(80) NOT NULL,
	member_id MEDIUMINT(8) NOT NULL,
	scope VARCHAR(2000) NULL DEFAULT NULL,
	created_at TIMESTAMP NOT NULL,
	CONSTRAINT pk_members PRIMARY KEY (client_id, member_id)
)';
