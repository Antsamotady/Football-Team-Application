-- Adminer 4.8.1 PostgreSQL 13.5 dump


INSERT INTO "annuite" ("id", "pays", "code_pays", "periode", "montants", "region", "name", "nameen", "actif", "isdeleted", "taxe", "type", "id_backend") VALUES
(58,	'AL',	NULL,	'14,545,45,6,6,7',	'422',	'PCT',	'Albanie',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(59,	'BG',	NULL,	'144,15',	'542',	'IT – change ',	'Braga',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(60,	'BR',	NULL,	'4421456',	'457',	'PC',	'Brasil',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(61,	'QL',	NULL,	'124,5445,54,54,44',	'555',	'truc',	'Endroit',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);



DROP TABLE IF EXISTS "doctrine_migration_versions";
CREATE TABLE "public"."doctrine_migration_versions" (
    "version" character varying(191) NOT NULL,
    "executed_at" timestamp(0),
    "execution_time" integer,
    CONSTRAINT "doctrine_migration_versions_pkey" PRIMARY KEY ("version")
) WITH (oids = false);

INSERT INTO "doctrine_migration_versions" ("version", "executed_at", "execution_time") VALUES
('DoctrineMigrations\Version20220301220116',	'2022-03-09 13:01:54',	40),
('DoctrineMigrations\Version20220309131024',	'2022-03-09 13:10:43',	26),
('DoctrineMigrations\Version20220309134113',	'2022-03-09 13:42:03',	18),
('DoctrineMigrations\Version20220309141059',	'2022-03-09 14:17:22',	13),
('DoctrineMigrations\Version20220309141711',	'2022-03-09 14:17:22',	2),
('DoctrineMigrations\Version20220310054014',	'2022-03-10 05:40:33',	12),
('DoctrineMigrations\Version20220317143251',	'2022-03-17 14:33:43',	33),
('DoctrineMigrations\Version20220318122849',	'2022-03-18 12:29:36',	33),
('DoctrineMigrations\Version20220318124257',	'2022-03-18 12:43:13',	23),
('DoctrineMigrations\Version20220318125344',	'2022-03-18 12:53:49',	24),
('DoctrineMigrations\Version20220318133930',	'2022-03-18 13:40:02',	39),
('DoctrineMigrations\Version20220324123955',	'2022-03-24 12:40:24',	29),
('DoctrineMigrations\Version20220510125136',	'2022-05-10 12:53:06',	32),
('DoctrineMigrations\Version20220510130119',	'2022-05-10 13:01:24',	13),
('DoctrineMigrations\Version20220510131812',	'2022-05-10 13:18:20',	32),
('DoctrineMigrations\Version20220510131842',	'2022-05-10 13:18:46',	19),
('DoctrineMigrations\Version20220510132938',	'2022-05-10 13:29:47',	31),
('DoctrineMigrations\Version20220510133112',	'2022-05-10 13:31:15',	21),
('DoctrineMigrations\Version20220510133654',	'2022-05-10 13:36:58',	23),
('DoctrineMigrations\Version20220511092653',	'2022-05-11 09:27:38',	28),
('DoctrineMigrations\Version20220511120131',	'2022-05-11 12:01:38',	19),
('DoctrineMigrations\Version20220530122846',	'2022-05-30 15:43:51',	18),
('DoctrineMigrations\Version20220530154334',	'2022-05-30 15:43:51',	12),
('DoctrineMigrations\Version20220726132620',	'2022-07-26 13:26:47',	16),
('DoctrineMigrations\Version20220816074232',	'2022-08-16 07:42:54',	18),
('DoctrineMigrations\Version20220816075143',	'2022-08-16 07:51:58',	25),
('DoctrineMigrations\Version20230530081105',	'2023-05-30 08:17:21',	39),
('DoctrineMigrations\Version20230530090731',	'2023-05-30 09:07:47',	39),
('DoctrineMigrations\Version20230530125647',	'2023-05-30 12:57:42',	30),
('DoctrineMigrations\Version20230531084726',	'2023-05-31 08:47:36',	23),
('DoctrineMigrations\Version20230531093718',	'2023-05-31 09:51:10',	19),
('DoctrineMigrations\Version20230531095321',	'2023-05-31 09:54:04',	17),
('DoctrineMigrations\Version20230531100850',	'2023-05-31 10:09:47',	19),
('DoctrineMigrations\Version20230531111030',	'2023-05-31 11:14:55',	34);




DROP TABLE IF EXISTS "player";
CREATE TABLE "public"."player" (
    "id" integer NOT NULL,
    "name" character varying(20) NOT NULL,
    "surname" character varying(20),
    "team_id" integer,
    "price" numeric(10,2),
    "is_available_for_sale" boolean DEFAULT true NOT NULL,
    CONSTRAINT "player_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

CREATE INDEX "idx_98197a65296cd8ae" ON "public"."player" USING btree ("team_id");

INSERT INTO "player" ("id", "name", "surname", "team_id", "price", "is_available_for_sale") VALUES
(383,	'Janie',	'Howell',	57,	73769.00,	'0'),
(380,	'Favian',	'McKenzie',	60,	11025.00,	'0'),
(392,	'Albina',	'Cormier',	60,	16147.00,	'0'),
(395,	'Brendon',	'Will',	60,	54743.00,	'0'),
(396,	'Samson',	'Roob',	60,	8249.00,	'0'),
(397,	'Justina',	'Smith',	60,	92349.00,	'0'),
(398,	'Jacquelyn',	'Abernathy',	60,	63318.00,	'1'),
(405,	'Cortney',	'Larkin',	60,	98240.00,	'1'),
(406,	'Reba',	'Gaylord',	60,	59299.00,	'1'),
(409,	'Albertha',	'Bradtke',	60,	37227.00,	'1'),
(411,	'Marielle',	'Farrell',	60,	54272.00,	'1'),
(413,	'Etha',	'Friesen',	60,	76224.00,	'0'),
(416,	'Joana',	'Hintz',	60,	25888.00,	'0'),
(389,	'Bernita',	'Turcotte',	57,	8651.00,	'0'),
(390,	'Guy',	'Weimann',	59,	1453.00,	'0'),
(403,	'Gabrielle',	'Schuster',	59,	91014.00,	'0'),
(381,	'Jamel',	'Predovic',	55,	99998.00,	'0'),
(382,	'Retta',	'Will',	55,	85389.00,	'0'),
(385,	'Joesph',	'Balistreri',	55,	10054.00,	'1'),
(387,	'Arvel',	'Nienow',	56,	77903.00,	'1'),
(388,	'Reginald',	'Heathcote',	NULL,	59526.00,	'0'),
(391,	'Ada',	'Berge',	56,	98528.00,	'1'),
(393,	'Brian',	'Gulgowski',	56,	17995.00,	'1'),
(386,	'Weldon',	'Schaden',	59,	95145.00,	'0'),
(402,	'Liana',	'Johnston',	59,	65998.00,	'0'),
(379,	'Ariel',	'Bradtkette',	60,	88125.00,	'1'),
(384,	'Magnolia',	'Swaniawski',	63,	80695.00,	'0'),
(399,	'Abbey',	'Von',	63,	47759.00,	'1'),
(401,	'Mose',	'Mertz',	63,	29411.00,	'0'),
(400,	'Mckenna',	'Padberg',	60,	28417.00,	'1'),
(410,	'Norval',	'Beier',	60,	88854.00,	'1'),
(394,	'Abby',	'Goyette',	NULL,	91568.00,	'0'),
(377,	'Virgil',	'Veum',	60,	79556.00,	'1'),
(404,	'Reynold',	'Grant',	NULL,	18926.00,	'0'),
(407,	'Hollie',	'Lockman',	58,	35339.00,	'1'),
(408,	'Tatyana',	'Braun',	58,	36504.00,	'1'),
(412,	'Rosalia',	'Thompson',	59,	76502.00,	'1'),
(414,	'Rebeka',	'Turcotte',	59,	96498.00,	'0'),
(415,	'Lennie',	'Fahey',	59,	81718.00,	'1'),
(417,	'Aliza',	'Mayer',	NULL,	39417.00,	'0'),
(418,	'Kaycee',	'Kautzer',	59,	32531.00,	'0');

DROP TABLE IF EXISTS "team";
CREATE TABLE "public"."team" (
    "id" integer NOT NULL,
    "name" character varying(16) NOT NULL,
    "country" character varying(20) NOT NULL,
    "money" numeric(10,2) NOT NULL,
    CONSTRAINT "team_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "team" ("id", "name", "country", "money") VALUES
(60,	'Manual Team',	'Bavania',	92345.00),
(56,	'Natus',	'Greenland',	125813.00),
(59,	'Minima',	'Mongolia',	7946457.00),
(58,	'Saepe',	'Liberia',	11682.00),
(63,	'Delete Team',	'Bavania2',	-1500.00),
(55,	'Voluptatum',	'Anguilla',	58086.00),
(57,	'Enim',	'Anguilla',	82663.00);


DROP TABLE IF EXISTS "user";
CREATE TABLE "public"."user" (
    "id" integer NOT NULL,
    "email" character varying(256) NOT NULL,
    "roles" json NOT NULL,
    "password" character varying(255) NOT NULL,
    "name" character varying(64),
    "totp_secret" character varying(16),
    "enabled" smallint,
    "locked" smallint,
    "expired" smallint,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    "keycompte_id" integer,
    "currency_id" integer,
    CONSTRAINT "uniq_8d93d649e7927c74" UNIQUE ("email"),
    CONSTRAINT "user_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

COMMENT ON COLUMN "public"."user"."created_at" IS '(DC2Type:datetime_immutable)';

COMMENT ON COLUMN "public"."user"."updated_at" IS '(DC2Type:datetime_immutable)';

INSERT INTO "user" ("id", "email", "roles", "password", "name", "totp_secret", "enabled", "locked", "expired", "created_at", "updated_at", "keycompte_id", "currency_id") VALUES
(1,	'test@mail.com',	'[]',	'$2y$13$JvcIhnEi69lIVa4G5GDWQuCC/vNVrT2ZEV/1J0OUIVnjblA6Vrq1m',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

ALTER TABLE ONLY "public"."player" ADD CONSTRAINT "fk_98197a65296cd8ae" FOREIGN KEY (team_id) REFERENCES team(id) NOT DEFERRABLE;

-- 2023-06-01 14:54:45.728486+00
