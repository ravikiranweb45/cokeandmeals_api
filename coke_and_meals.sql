--
-- PostgreSQL database dump
--

-- Dumped from database version 11.13
-- Dumped by pg_dump version 11.13

-- Started on 2022-08-01 12:08:09

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 203 (class 1259 OID 290449)
-- Name: classifications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.classifications (
    id integer NOT NULL,
    class_name character varying(150),
    class_desc text,
    class_order integer,
    status smallint DEFAULT 1,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
);


ALTER TABLE public.classifications OWNER TO postgres;

--
-- TOC entry 2957 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN classifications.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.classifications.status IS '1 - Active
0 - In Active';


--
-- TOC entry 202 (class 1259 OID 290447)
-- Name: classifications_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.classifications_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.classifications_id_seq OWNER TO postgres;

--
-- TOC entry 2958 (class 0 OID 0)
-- Dependencies: 202
-- Name: classifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.classifications_id_seq OWNED BY public.classifications.id;


--
-- TOC entry 201 (class 1259 OID 290437)
-- Name: customer_login_history; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer_login_history (
    id integer NOT NULL,
    customer_id integer,
    loginat timestamp without time zone,
    otp text,
    status smallint DEFAULT 0,
    phone_number character varying(20),
    access_token text,
    ip_address character varying(100)
);


ALTER TABLE public.customer_login_history OWNER TO postgres;

--
-- TOC entry 2959 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN customer_login_history.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.customer_login_history.status IS '0 - Not verified
1 - Verified';


--
-- TOC entry 200 (class 1259 OID 290435)
-- Name: customer_login_history_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customer_login_history_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customer_login_history_id_seq OWNER TO postgres;

--
-- TOC entry 2960 (class 0 OID 0)
-- Dependencies: 200
-- Name: customer_login_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.customer_login_history_id_seq OWNED BY public.customer_login_history.id;


--
-- TOC entry 211 (class 1259 OID 290498)
-- Name: customer_transactions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer_transactions (
    id bigint NOT NULL,
    customer_id integer,
    restaurant_offer_id bigint,
    transaction_code text,
    status smallint DEFAULT 1,
    latitude text,
    longitude text,
    customer_mobile_no character varying(20),
    restaurant_mobile_no character varying(20),
    created_date timestamp without time zone,
    updated_date timestamp without time zone
);


ALTER TABLE public.customer_transactions OWNER TO postgres;

--
-- TOC entry 2961 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN customer_transactions.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.customer_transactions.status IS '0 - In Active
1 - Active';


--
-- TOC entry 210 (class 1259 OID 290496)
-- Name: customer_transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customer_transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customer_transactions_id_seq OWNER TO postgres;

--
-- TOC entry 2962 (class 0 OID 0)
-- Dependencies: 210
-- Name: customer_transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.customer_transactions_id_seq OWNED BY public.customer_transactions.id;


--
-- TOC entry 213 (class 1259 OID 290512)
-- Name: customer_wishlist; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer_wishlist (
    id integer NOT NULL,
    customer_id integer,
    restaurant_offer_id bigint,
    status smallint DEFAULT 1,
    created_date timestamp without time zone
);


ALTER TABLE public.customer_wishlist OWNER TO postgres;

--
-- TOC entry 2963 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN customer_wishlist.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.customer_wishlist.status IS '0 - In Active
1 - Active';


--
-- TOC entry 212 (class 1259 OID 290510)
-- Name: customer_wishlist_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customer_wishlist_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customer_wishlist_id_seq OWNER TO postgres;

--
-- TOC entry 2964 (class 0 OID 0)
-- Dependencies: 212
-- Name: customer_wishlist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.customer_wishlist_id_seq OWNED BY public.customer_wishlist.id;


--
-- TOC entry 197 (class 1259 OID 290409)
-- Name: customers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customers (
    id integer NOT NULL,
    customer_name character varying(150),
    mobile_no character varying(20),
    dob date,
    city character varying(250),
    address text,
    state_id integer,
    pincode integer,
    gender character(1),
    status smallint DEFAULT 1,
    created_date timestamp without time zone,
    updated_date timestamp without time zone,
    access_token text,
    access_token_expiry timestamp without time zone,
    email text,
    default_lang character varying(10)
);


ALTER TABLE public.customers OWNER TO postgres;

--
-- TOC entry 2965 (class 0 OID 0)
-- Dependencies: 197
-- Name: COLUMN customers.gender; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.customers.gender IS 'M- Male
F - Female
T - Trans Gender';


--
-- TOC entry 2966 (class 0 OID 0)
-- Dependencies: 197
-- Name: COLUMN customers.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.customers.status IS '1 - Active
0 - In Active';


--
-- TOC entry 196 (class 1259 OID 290407)
-- Name: customers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customers_id_seq OWNER TO postgres;

--
-- TOC entry 2967 (class 0 OID 0)
-- Dependencies: 196
-- Name: customers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.customers_id_seq OWNED BY public.customers.id;


--
-- TOC entry 215 (class 1259 OID 290521)
-- Name: messages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.messages (
    id integer NOT NULL,
    createdon timestamp(0) without time zone NOT NULL,
    fromid character varying(50) DEFAULT NULL::character varying,
    toid character varying(50) NOT NULL,
    sub character varying(250) DEFAULT NULL::character varying,
    body text NOT NULL,
    msgtype smallint NOT NULL,
    senton timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    status smallint NOT NULL,
    campaign_id integer,
    program_id integer,
    template_id text,
    menu_file_path text
);


ALTER TABLE public.messages OWNER TO postgres;

--
-- TOC entry 214 (class 1259 OID 290519)
-- Name: messages_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.messages_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.messages_id_seq OWNER TO postgres;

--
-- TOC entry 2968 (class 0 OID 0)
-- Dependencies: 214
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.messages_id_seq OWNED BY public.messages.id;


--
-- TOC entry 207 (class 1259 OID 290475)
-- Name: offers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.offers (
    id integer NOT NULL,
    offer_name text,
    offer_code character varying(150),
    discount_value numeric(20,2),
    brands_involved text,
    is_combo_offer smallint DEFAULT 0,
    status smallint DEFAULT 1,
    created_date timestamp without time zone,
    updated_date timestamp without time zone,
    is_spl_offer smallint
);


ALTER TABLE public.offers OWNER TO postgres;

--
-- TOC entry 2969 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN offers.is_combo_offer; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.offers.is_combo_offer IS '0 - No
1 - Yes';


--
-- TOC entry 2970 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN offers.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.offers.status IS '0 - In Active
1 - Active';


--
-- TOC entry 2971 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN offers.is_spl_offer; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.offers.is_spl_offer IS '0 - No
1 - Yes';


--
-- TOC entry 206 (class 1259 OID 290473)
-- Name: offers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.offers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.offers_id_seq OWNER TO postgres;

--
-- TOC entry 2972 (class 0 OID 0)
-- Dependencies: 206
-- Name: offers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.offers_id_seq OWNED BY public.offers.id;


--
-- TOC entry 209 (class 1259 OID 290488)
-- Name: restaurant_offers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.restaurant_offers (
    id bigint NOT NULL,
    restaurant_id integer,
    offer_id integer,
    start_date date,
    end_date date,
    classification_id integer,
    is_spl_occasion smallint DEFAULT 0,
    total_scan_count integer,
    status smallint DEFAULT 1,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
);


ALTER TABLE public.restaurant_offers OWNER TO postgres;

--
-- TOC entry 2973 (class 0 OID 0)
-- Dependencies: 209
-- Name: COLUMN restaurant_offers.is_spl_occasion; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.restaurant_offers.is_spl_occasion IS '0 - No
1 - Yes';


--
-- TOC entry 2974 (class 0 OID 0)
-- Dependencies: 209
-- Name: COLUMN restaurant_offers.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.restaurant_offers.status IS '0 - In Active
1 - Active';


--
-- TOC entry 208 (class 1259 OID 290486)
-- Name: restaurant_offers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.restaurant_offers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.restaurant_offers_id_seq OWNER TO postgres;

--
-- TOC entry 2975 (class 0 OID 0)
-- Dependencies: 208
-- Name: restaurant_offers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.restaurant_offers_id_seq OWNED BY public.restaurant_offers.id;


--
-- TOC entry 199 (class 1259 OID 290423)
-- Name: restaurant_qr_codes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.restaurant_qr_codes (
    id bigint NOT NULL,
    restaurant_id integer,
    unique_code text,
    expiry_date timestamp without time zone,
    status smallint DEFAULT 1,
    created_date timestamp without time zone,
    updated_date timestamp without time zone,
    start_date date,
    end_date timestamp without time zone
);


ALTER TABLE public.restaurant_qr_codes OWNER TO postgres;

--
-- TOC entry 2976 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN restaurant_qr_codes.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.restaurant_qr_codes.status IS '1 - Active
0 - In Active';


--
-- TOC entry 198 (class 1259 OID 290421)
-- Name: restaurant_qr_codes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.restaurant_qr_codes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.restaurant_qr_codes_id_seq OWNER TO postgres;

--
-- TOC entry 2977 (class 0 OID 0)
-- Dependencies: 198
-- Name: restaurant_qr_codes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.restaurant_qr_codes_id_seq OWNED BY public.restaurant_qr_codes.id;


--
-- TOC entry 205 (class 1259 OID 290461)
-- Name: restaurants; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.restaurants (
    id integer NOT NULL,
    restaurant_name character varying(250),
    restaurant_code character varying(100),
    latitude text,
    longitude text,
    restaurant_image text,
    restaurant_tags text,
    address text,
    city character varying(250),
    state_id integer,
    country_id integer,
    pincode integer,
    mobile_no character varying(20),
    website_url text,
    classification_ids integer[],
    restaurant_content text,
    description text,
    mon_open time without time zone,
    mon_close time without time zone,
    tue_open time without time zone,
    tue_close time without time zone,
    wed_open time without time zone,
    wed_close time without time zone,
    thu_open time without time zone,
    thu_close time without time zone,
    fri_open time without time zone,
    fri_close time without time zone,
    sat_open time without time zone,
    sat_close time without time zone,
    sun_open time without time zone,
    sun_close time without time zone,
    status smallint DEFAULT 1,
    created_date timestamp without time zone,
    updated_date timestamp without time zone
);


ALTER TABLE public.restaurants OWNER TO postgres;

--
-- TOC entry 2978 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN restaurants.status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.restaurants.status IS '0 - In Active
1 - Active';


--
-- TOC entry 204 (class 1259 OID 290459)
-- Name: restaurants_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.restaurants_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.restaurants_id_seq OWNER TO postgres;

--
-- TOC entry 2979 (class 0 OID 0)
-- Dependencies: 204
-- Name: restaurants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.restaurants_id_seq OWNED BY public.restaurants.id;


--
-- TOC entry 216 (class 1259 OID 290537)
-- Name: states; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.states (
    id integer NOT NULL,
    state_name character varying(100),
    state_code character varying(10),
    status smallint DEFAULT 1 NOT NULL,
    created_date timestamp without time zone
);


ALTER TABLE public.states OWNER TO postgres;

--
-- TOC entry 2757 (class 2604 OID 290452)
-- Name: classifications id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.classifications ALTER COLUMN id SET DEFAULT nextval('public.classifications_id_seq'::regclass);


--
-- TOC entry 2755 (class 2604 OID 290440)
-- Name: customer_login_history id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_login_history ALTER COLUMN id SET DEFAULT nextval('public.customer_login_history_id_seq'::regclass);


--
-- TOC entry 2767 (class 2604 OID 290501)
-- Name: customer_transactions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_transactions ALTER COLUMN id SET DEFAULT nextval('public.customer_transactions_id_seq'::regclass);


--
-- TOC entry 2769 (class 2604 OID 290515)
-- Name: customer_wishlist id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_wishlist ALTER COLUMN id SET DEFAULT nextval('public.customer_wishlist_id_seq'::regclass);


--
-- TOC entry 2751 (class 2604 OID 290412)
-- Name: customers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customers ALTER COLUMN id SET DEFAULT nextval('public.customers_id_seq'::regclass);


--
-- TOC entry 2771 (class 2604 OID 290524)
-- Name: messages id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages ALTER COLUMN id SET DEFAULT nextval('public.messages_id_seq'::regclass);


--
-- TOC entry 2761 (class 2604 OID 290478)
-- Name: offers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.offers ALTER COLUMN id SET DEFAULT nextval('public.offers_id_seq'::regclass);


--
-- TOC entry 2764 (class 2604 OID 290491)
-- Name: restaurant_offers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurant_offers ALTER COLUMN id SET DEFAULT nextval('public.restaurant_offers_id_seq'::regclass);


--
-- TOC entry 2753 (class 2604 OID 290426)
-- Name: restaurant_qr_codes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurant_qr_codes ALTER COLUMN id SET DEFAULT nextval('public.restaurant_qr_codes_id_seq'::regclass);


--
-- TOC entry 2759 (class 2604 OID 290464)
-- Name: restaurants id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurants ALTER COLUMN id SET DEFAULT nextval('public.restaurants_id_seq'::regclass);


--
-- TOC entry 2938 (class 0 OID 290449)
-- Dependencies: 203
-- Data for Name: classifications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.classifications (id, class_name, class_desc, class_order, status, created_date, updated_date) FROM stdin;
\.


--
-- TOC entry 2936 (class 0 OID 290437)
-- Dependencies: 201
-- Data for Name: customer_login_history; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customer_login_history (id, customer_id, loginat, otp, status, phone_number, access_token, ip_address) FROM stdin;
\.


--
-- TOC entry 2946 (class 0 OID 290498)
-- Dependencies: 211
-- Data for Name: customer_transactions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customer_transactions (id, customer_id, restaurant_offer_id, transaction_code, status, latitude, longitude, customer_mobile_no, restaurant_mobile_no, created_date, updated_date) FROM stdin;
\.


--
-- TOC entry 2948 (class 0 OID 290512)
-- Dependencies: 213
-- Data for Name: customer_wishlist; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customer_wishlist (id, customer_id, restaurant_offer_id, status, created_date) FROM stdin;
\.


--
-- TOC entry 2932 (class 0 OID 290409)
-- Dependencies: 197
-- Data for Name: customers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customers (id, customer_name, mobile_no, dob, city, address, state_id, pincode, gender, status, created_date, updated_date, access_token, access_token_expiry, email, default_lang) FROM stdin;
\.


--
-- TOC entry 2950 (class 0 OID 290521)
-- Dependencies: 215
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.messages (id, createdon, fromid, toid, sub, body, msgtype, senton, status, campaign_id, program_id, template_id, menu_file_path) FROM stdin;
\.


--
-- TOC entry 2942 (class 0 OID 290475)
-- Dependencies: 207
-- Data for Name: offers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.offers (id, offer_name, offer_code, discount_value, brands_involved, is_combo_offer, status, created_date, updated_date, is_spl_offer) FROM stdin;
\.


--
-- TOC entry 2944 (class 0 OID 290488)
-- Dependencies: 209
-- Data for Name: restaurant_offers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.restaurant_offers (id, restaurant_id, offer_id, start_date, end_date, classification_id, is_spl_occasion, total_scan_count, status, created_date, updated_date) FROM stdin;
\.


--
-- TOC entry 2934 (class 0 OID 290423)
-- Dependencies: 199
-- Data for Name: restaurant_qr_codes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.restaurant_qr_codes (id, restaurant_id, unique_code, expiry_date, status, created_date, updated_date, start_date, end_date) FROM stdin;
\.


--
-- TOC entry 2940 (class 0 OID 290461)
-- Dependencies: 205
-- Data for Name: restaurants; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.restaurants (id, restaurant_name, restaurant_code, latitude, longitude, restaurant_image, restaurant_tags, address, city, state_id, country_id, pincode, mobile_no, website_url, classification_ids, restaurant_content, description, mon_open, mon_close, tue_open, tue_close, wed_open, wed_close, thu_open, thu_close, fri_open, fri_close, sat_open, sat_close, sun_open, sun_close, status, created_date, updated_date) FROM stdin;
1	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	1	\N	\N
\.


--
-- TOC entry 2951 (class 0 OID 290537)
-- Dependencies: 216
-- Data for Name: states; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.states (id, state_name, state_code, status, created_date) FROM stdin;
\.


--
-- TOC entry 2980 (class 0 OID 0)
-- Dependencies: 202
-- Name: classifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.classifications_id_seq', 1, false);


--
-- TOC entry 2981 (class 0 OID 0)
-- Dependencies: 200
-- Name: customer_login_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customer_login_history_id_seq', 1, false);


--
-- TOC entry 2982 (class 0 OID 0)
-- Dependencies: 210
-- Name: customer_transactions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customer_transactions_id_seq', 1, false);


--
-- TOC entry 2983 (class 0 OID 0)
-- Dependencies: 212
-- Name: customer_wishlist_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customer_wishlist_id_seq', 1, false);


--
-- TOC entry 2984 (class 0 OID 0)
-- Dependencies: 196
-- Name: customers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customers_id_seq', 1, false);


--
-- TOC entry 2985 (class 0 OID 0)
-- Dependencies: 214
-- Name: messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.messages_id_seq', 1, false);


--
-- TOC entry 2986 (class 0 OID 0)
-- Dependencies: 206
-- Name: offers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.offers_id_seq', 1, false);


--
-- TOC entry 2987 (class 0 OID 0)
-- Dependencies: 208
-- Name: restaurant_offers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.restaurant_offers_id_seq', 1, false);


--
-- TOC entry 2988 (class 0 OID 0)
-- Dependencies: 198
-- Name: restaurant_qr_codes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.restaurant_qr_codes_id_seq', 1, false);


--
-- TOC entry 2989 (class 0 OID 0)
-- Dependencies: 204
-- Name: restaurants_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.restaurants_id_seq', 1, false);


--
-- TOC entry 2787 (class 2606 OID 290458)
-- Name: classifications classifications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.classifications
    ADD CONSTRAINT classifications_pkey PRIMARY KEY (id);


--
-- TOC entry 2785 (class 2606 OID 290446)
-- Name: customer_login_history customer_login_history_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_login_history
    ADD CONSTRAINT customer_login_history_pkey PRIMARY KEY (id);


--
-- TOC entry 2797 (class 2606 OID 290507)
-- Name: customer_transactions customer_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_transactions
    ADD CONSTRAINT customer_transactions_pkey PRIMARY KEY (id);


--
-- TOC entry 2799 (class 2606 OID 290509)
-- Name: customer_transactions customer_transactions_transaction_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_transactions
    ADD CONSTRAINT customer_transactions_transaction_code_key UNIQUE (transaction_code);


--
-- TOC entry 2801 (class 2606 OID 290518)
-- Name: customer_wishlist customer_wishlist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_wishlist
    ADD CONSTRAINT customer_wishlist_pkey PRIMARY KEY (id);


--
-- TOC entry 2777 (class 2606 OID 290420)
-- Name: customers customers_mobile_no_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customers
    ADD CONSTRAINT customers_mobile_no_key UNIQUE (mobile_no);


--
-- TOC entry 2779 (class 2606 OID 290418)
-- Name: customers customers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customers
    ADD CONSTRAINT customers_pkey PRIMARY KEY (id);


--
-- TOC entry 2803 (class 2606 OID 290532)
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- TOC entry 2793 (class 2606 OID 290485)
-- Name: offers offers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.offers
    ADD CONSTRAINT offers_pkey PRIMARY KEY (id);


--
-- TOC entry 2795 (class 2606 OID 290495)
-- Name: restaurant_offers restaurant_offers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurant_offers
    ADD CONSTRAINT restaurant_offers_pkey PRIMARY KEY (id);


--
-- TOC entry 2781 (class 2606 OID 290432)
-- Name: restaurant_qr_codes restaurant_qr_codes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurant_qr_codes
    ADD CONSTRAINT restaurant_qr_codes_pkey PRIMARY KEY (id);


--
-- TOC entry 2783 (class 2606 OID 290434)
-- Name: restaurant_qr_codes restaurant_qr_codes_unique_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurant_qr_codes
    ADD CONSTRAINT restaurant_qr_codes_unique_code_key UNIQUE (unique_code);


--
-- TOC entry 2789 (class 2606 OID 290470)
-- Name: restaurants restaurants_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurants
    ADD CONSTRAINT restaurants_pkey PRIMARY KEY (id);


--
-- TOC entry 2791 (class 2606 OID 290472)
-- Name: restaurants restaurants_restaurant_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.restaurants
    ADD CONSTRAINT restaurants_restaurant_code_key UNIQUE (restaurant_code);


--
-- TOC entry 2809 (class 2606 OID 290542)
-- Name: states state_id_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.states
    ADD CONSTRAINT state_id_pkey PRIMARY KEY (id);


--
-- TOC entry 2804 (class 1259 OID 290533)
-- Name: msg_campaign_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX msg_campaign_id ON public.messages USING btree (campaign_id);


--
-- TOC entry 2805 (class 1259 OID 290534)
-- Name: msgtypeidx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX msgtypeidx ON public.messages USING btree (msgtype, status);


--
-- TOC entry 2806 (class 1259 OID 290535)
-- Name: senton; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX senton ON public.messages USING btree (senton);


--
-- TOC entry 2807 (class 1259 OID 290536)
-- Name: toid; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX toid ON public.messages USING btree (toid);


-- Completed on 2022-08-01 12:08:09

--
-- PostgreSQL database dump complete
--

