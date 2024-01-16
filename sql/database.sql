CREATE DATABASE detik_lokal;

CREATE TABLE detik_lokal.transactions (
    id varchar(64) NOT NULL primary key,
    invoice_id varchar(50) NOT NULL,
    item_name varchar(200) NOT NULL,
    amount bigint DEFAULT 0,
    merchant_id varchar(64) NOT NULL,
    payment_type varchar(15) NOT NULL DEFAULT 'virtual_account',
    customer_name varchar(150) NOT NULL,
    number_va varchar(10) NULL,
    references_id varchar(64) NULL,
    status varchar(10) DEFAULT 'Pending',
    created_at timestamp DEFAULT now(),
    updated_at timestamp NULL,
    deleted_at timestamp NULL
);

INSERT INTO detik_lokal.transactions (id, invoice_id, item_name, amount, merchant_id, payment_type, customer_name, number_va, references_id)
VALUES (UUID(), uuid(), 'tas', 200000, uuid(), 'credit_card', 'rini', null, uuid()),
       (uuid(), uuid(), 'dasi', 20000, uuid(), 'virtual_account', 'rani', 'qKZmqHVhks', uuid()),
       (uuid(), uuid(), 'baju', 100000, uuid(), 'credit_card', 'rina', null, uuid()),
       (uuid(), uuid(), 'kaos', 30000, uuid(), 'virtual_account', 'rino', 'uInC2zQvbL', uuid());