DROP TABLE users;
DROP TABLE recipes;

CREATE SEQUENCE public.users_id_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

CREATE TABLE users (
  id bigint UNIQUE NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  username character varying(32) NOT NULL,
  password character varying(64) NOT NULL,
  apikey character varying(255)  NOT NULL,
  -- apikey_salt character varying(100)  NOT NULL
);

CREATE SEQUENCE public.recipe_id_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

CREATE TABLE recipes (
  id bigint NOT NULL DEFAULT nextval('recipe_id_seq'::regclass),
  user_id  bigint NOT NULL,
  head character varying(64) NOT NULL,
  body character varying(255) NOT NULL,
  image_url character varying(100),


  CONSTRAINT user_recipe_fkey FOREIGN KEY (user_id)
  REFERENCES public.users (id) MATCH SIMPLE
  ON UPDATE NO ACTION ON DELETE CASCADE

);
ALTER TABLE recipes ADD COLUMN image_upload_url character varying(100);

INSERT INTO users (username, password, apikey, apikey_salt) VALUES ('pet1', 'pet1', 'qqq123', '123');
INSERT INTO users (username, password, apikey, apikey_salt) VALUES ('vas1', 'vas1', 'jjjqqq123', '123');

SELECT * FROM users

INSERT INTO recipes (user_id, head, body) VALUES (54, 'Recipe of water', 'Desc recipe');
INSERT INTO recipes (user_id, head, body) VALUES (55, 'Recipe of sugar', 'Desc recipe - sugar');

SELECT * FROM recipes

