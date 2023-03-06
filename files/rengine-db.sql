-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-03-2023 a las 07:49:19
-- Versión del servidor: 10.3.38-MariaDB-log
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hotelisa_rengine`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` int(10) UNSIGNED NOT NULL,
  `discount_code` varchar(100) NOT NULL,
  `percentage` int(10) UNSIGNED NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `discounts`
--

INSERT INTO `discounts` (`discount_id`, `discount_code`, `percentage`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 'ISABEL', 35, '2022-05-02 11:40:00', '2022-05-31 11:40:00', '2022-04-29 09:40:53', '2022-05-02 08:55:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `folio` varchar(30) NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `num_rooms` int(11) NOT NULL,
  `check_in` varchar(10) NOT NULL,
  `check_out` varchar(10) NOT NULL,
  `re_name` varchar(100) NOT NULL,
  `re_surname` varchar(100) NOT NULL,
  `re_email` varchar(60) NOT NULL,
  `re_phone` varchar(60) NOT NULL,
  `payment_method` enum('Card','SPEI') NOT NULL,
  `cc_name` varchar(100) DEFAULT NULL,
  `cc_number` varchar(19) DEFAULT NULL,
  `cc_expires` varchar(7) DEFAULT NULL,
  `cc_cvv` varchar(4) DEFAULT NULL,
  `observations_guest` text DEFAULT NULL,
  `observations_hotel` text DEFAULT NULL,
  `subtotal` float NOT NULL,
  `total` float NOT NULL,
  `status` enum('NO CONFIRMADA','CONFIRMADA','CANCELADA') NOT NULL DEFAULT 'NO CONFIRMADA',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `folio`, `room_id`, `num_rooms`, `check_in`, `check_out`, `re_name`, `re_surname`, `re_email`, `re_phone`, `payment_method`, `cc_name`, `cc_number`, `cc_expires`, `cc_cvv`, `observations_guest`, `observations_hotel`, `subtotal`, `total`, `status`, `created_at`, `updated_at`) VALUES
(1, '626c1420a4170', 8, 1, '2022-04-29', '2022-04-30', 'CARLOS', 'HERNANDEZ', 'CARLOSALEXIS999@HOTMAIL.COM', '3338066730', 'SPEI', 'CARLOS ALEXIS ', '3328429112', '02/02', '258', 'PLANTA BAJA PORFAVOR', 'ni modo', 1090, 1297.1, 'CANCELADA', '2022-04-29 09:36:48', '2022-04-29 09:39:46'),
(2, '6286a74684104', 9, 1, '2022-05-28', '2022-05-29', 'Maria del Socorro', 'Herrera Jiménez', 'kokoh27@hotmail.com', '3781025356', 'Card', 'maria del socorro herrera jimenez', '4213646220067560', '06/24', '226', 'De preferencia en el área remodelada', 'Realizó transferencia', 708.5, 843.115, 'CONFIRMADA', '2022-05-19 13:23:34', '2022-05-28 06:58:42'),
(3, '6287be7ede896', 9, 1, '2022-05-20', '2022-05-22', 'Remi', 'David', 'remi0208@gmail.com', '9581250529', 'Card', 'Remi David', '4165981524373242', '/11/202', '657', '', 'SIN DISPONIBILIDAD PARA EL DIA 20/05/22', 1417, 1686.23, 'CANCELADA', '2022-05-20 09:14:55', '2022-05-20 09:19:51'),
(4, '62894dc7640b8', 9, 2, '2022-05-21', '2022-05-23', 'Rebeca', 'Hernandez', 'ariess_65@hotmail.com', '5534335472', 'Card', 'María Cruz ', '5522631015935828', '10/22', '680', 'Buenas noches ardes\r\nOcupo 3 habitaciones por favor ', 'No show', 2349.1, 2795.43, 'CANCELADA', '2022-05-21 13:38:31', '2022-05-22 14:28:06'),
(5, '62894e91f225e', 8, 2, '2022-05-21', '2022-05-23', 'Rebeca', 'Hernandez', 'ariess_65@hotmail.com', '5534335472', 'Card', 'María cruz', '5522631015935828', '10/22', '68', '', 'No show', 1799.85, 2141.82, 'CANCELADA', '2022-05-21 13:41:54', '2022-05-22 14:28:28'),
(6, '62897481e1e44', 8, 1, '2022-05-22', '2022-05-25', 'CRISTIAN AARON', 'BERNARDINO QUEZADAS', 'vinculos_cristian@hotmail.com', '554042600', 'Card', 'Cristiann Aaron Bernardino', '4815 1630 3964 0522', '09/23', '727', '', 'Petición del huesped', 1930.5, 2297.29, 'CANCELADA', '2022-05-21 16:23:46', '2022-05-22 14:27:23'),
(7, '628c831d7f170', 8, 1, '2022-10-16', '2022-10-24', 'Jeffrey', 'Roy', 'royj73@hotmail.com', '+1 916-342-4710', 'Card', '', '', '/', '', 'Request availability for Room 508. ', 'si disponibilidad para la fecha', 5148, 6126.12, 'CANCELADA', '2022-05-24 00:02:53', '2022-07-14 09:14:09'),
(8, '629288b1de443', 9, 1, '2022-05-28', '2022-05-30', 'Nancy Paola', 'Bernal', 'nancypaolabernalpalomera@gmail.com', '3222797696', 'Card', 'Bancomer', '4152313485595412', '03/24', '834', '', 'Una disculpa el hotel se encuentra lleno en estos momentos por lo consiguiente su reservación no puede ser concretada.', 1807, 2150.33, 'CANCELADA', '2022-05-28 13:40:18', '2022-05-28 14:07:36'),
(9, '629668fdc05d2', 9, 1, '2022-06-07', '2022-06-09', 'JONATHAN', 'MONTIEL LEON', 'cambioslasalud_sanfelipe@outlook.com', '4281015271', 'Card', 'Jonathan Montiel Leon', '4815163096219871', '10/2025', '057', '', 'GARANTIZADA 1 NOCHE', 1807, 2150.33, 'CONFIRMADA', '2022-05-31 12:14:06', '2022-06-03 13:01:06'),
(10, '629be8478a4aa', 9, 1, '2022-06-04', '2022-06-05', 'Angel Eduardo ', 'Monsiváis Zavala ', 'angelmzavala9@gmail.com', '811 166 5936 ', 'Card', 'Angel Monsivais Zavala ', '5204165699749390', '09/25', '415', '', '.', 806, 959.14, 'CANCELADA', '2022-06-04 16:18:31', '2022-06-06 06:03:32'),
(11, '629e4597c916e', 8, 1, '2022-06-09', '2022-06-10', 'Sara Erika ', 'Olivares Salazar', 'erikaolivares.31@gmail.com', '5535726798', 'Card', 'Sara Erika Olivares Salazar', '4815153021006071', '09/25', '027', '', 'GARANTIZADA', 643.5, 765.765, 'CONFIRMADA', '2022-06-06 11:21:12', '2022-06-08 07:59:52'),
(12, '62ab5373085b1', 9, 1, '2022-07-16', '2022-07-19', 'Sarah S Seyed-Ali', '', 'sseyedali001@gmail.com', '13019563286', 'Card', 'Sarah S Seyed-Ali ', '5278540018848878', '12/25', '689', '', 'TARJETA VALIDADA. NUMERO DE CONFIRMACIÓN DEL HOTEL:2014004-29517', 1930.5, 2297.29, 'CONFIRMADA', '2022-06-16 08:59:47', '2022-07-01 09:24:14'),
(13, '62abed4aa6425', 8, 1, '2022-06-17', '2022-06-18', 'Arturo', 'Rojas', 'arturoandersen@yahoo.com.mx', '5525636222', 'Card', 'Arturo Rojas Andersen', '370785375076005', '03/26', '4943', '', 'PAGADA', 676, 804.44, 'CONFIRMADA', '2022-06-16 19:56:11', '2022-06-17 14:13:32'),
(14, '62b29ac2d4617', 8, 1, '2022-06-25', '2022-06-27', 'FERNANDO RAFAEL', 'GARCIA MORA', 'arquimax80@gmail.com', '2281623838', 'Card', 'FERNANDO RAFAEL GARCIA MORA', '4815163073725056', '07/24', '729', '', 'NUMERO DE CONFIRMACIÓN: 1993004-29315', 1157, 1376.83, 'CONFIRMADA', '2022-06-21 21:29:55', '2022-06-23 09:15:21'),
(15, '62b698b01a040', 8, 1, '2022-07-02', '2022-07-08', 'Alberto', 'Larraguivel Omaña', 'albertolarraguivel@gmail.com', '7224154227', 'Card', 'Alberto Larraguivel Omaña', '4152313425200388', '08/23', '607', '', 'RVA CONFIRMADA: NUMERO DE CONFIRMACIÓN:2014004-29516', 4056, 4826.64, 'CONFIRMADA', '2022-06-24 22:10:08', '2022-07-01 09:14:34'),
(16, '62b722a4c2f43', 8, 1, '2022-06-25', '2022-06-26', 'Mario Ruben', 'Saucedo Orozco', 'mariomayle@icloud.com', '3121102966', 'Card', 'Mario Saucedo', '4918718001370626', '05/2024', '895', '', 'PASO FECHA', 676, 804.44, 'CANCELADA', '2022-06-25 07:58:45', '2022-06-27 07:26:50'),
(17, '62b749de8b42f', 9, 1, '2022-08-03', '2022-08-08', 'Gilberto ', 'Correa', 'ale18san1991@gmail.com', '6862390837', 'SPEI', '', '', '/', '', 'Reservar 1 doble y 3 sencillas', 'EL HOTEL YA NO CUENTA CON DISPONIBILIDAD PARA ESTAS FECHAS', 4192.5, 4989.08, 'CANCELADA', '2022-06-25 10:46:07', '2022-07-01 09:07:12'),
(18, '62b93dd253700', 8, 2, '2022-07-02', '2022-07-03', 'Edgar ', 'Baeza Tinajero', 'pkbaeza32@gmail.com', '4941181455', 'Card', 'Edgar Baeza', '5524335551318740', '10/26', '632', '', 'TARJETA VALIDA PARA GARANTÍA. NUMERO DE CONFIRMACIÓN DEL HOTEL: 2014004-29515', 878.8, 1045.77, 'CONFIRMADA', '2022-06-26 22:19:14', '2022-07-01 08:44:01'),
(19, '62c09884426bc', 8, 1, '2022-07-09', '2022-07-11', 'Ruben ', 'Galicia Rangel ', 'rugara539212@yahoo.com.mx', '5554370364', 'Card', 'José Rubén Galicia Rangel ', '4213166141491443', '04/26', '800', '', 'TARJETA NO VALIDA', 1157, 1376.83, 'CANCELADA', '2022-07-02 12:12:04', '2022-07-06 06:33:23'),
(20, '62c09a28d9f1e', 9, 2, '2022-07-15', '2022-07-17', 'RICARDO', 'Balderas Valencia', 'ricabava@yahoo.com.mx', '5539724477', 'SPEI', '', '', '/', '', '', 'realizo deposito', 2180.1, 2594.32, 'CONFIRMADA', '2022-07-02 12:19:05', '2022-07-12 08:16:48'),
(21, '62c0b26332261', 9, 1, '2022-07-06', '2022-07-08', 'Maria del Carmen', 'Martinez Mejia', 'martinez_mejia_e@hotmail.com', '4443136600', 'Card', 'Maria del Carmen Martinez Mejia', '4152313714095325', '08/25', '109', '', 'TARJETA VALIDADA', 1482, 1763.58, 'CONFIRMADA', '2022-07-02 14:02:27', '2022-07-05 10:36:45'),
(22, '62c0b4964f324', 8, 1, '2022-07-09', '2022-07-11', 'Ruben ', 'Galicia Rangel ', 'rugara539212@yahoo.com.mx', '5554370364', 'Card', 'José Rubén Galicia Rangel ', '4213166141491443', '04/26', '800', '', 'TARJETA NO VALIDA', 1157, 1376.83, 'CANCELADA', '2022-07-02 14:11:50', '2022-07-06 06:33:08'),
(23, '62c4bac08245e', 8, 1, '2022-07-19', '2022-07-24', 'CAMILA', 'LLOVERAS', 'marissa@ilmercato.mx', '8441953361', 'Card', '', '', '/', '', '', 'DEPOSITO VALIDADO', 2892.5, 3442.07, 'CONFIRMADA', '2022-07-05 15:27:12', '2022-07-13 14:42:49'),
(24, '62c6d993bd30b', 8, 1, '2022-07-08', '2022-07-11', 'María elena ', 'Hernández Ledesma', 'Mamalena56_pinta@hotmail.com ', '4192659517', 'Card', 'Santander master card', '5408453006836832', '06/26', '461', 'Ninguno', 'sin disponibilidad para la fecha', 2028, 2413.32, 'CANCELADA', '2022-07-07 06:03:16', '2022-07-08 10:06:19'),
(25, '62c8fa6693d10', 8, 1, '2022-07-30', '2022-08-02', 'Rafael', 'Aguilar González', 'aguilar.gonzalez.rafael@gmail.com', '4441349459', 'Card', 'Rafael Aguilar González', '4815153008649208', '09/23', '212', '', 'NUMERO DE CONFIRMACION DEL HOTEL: 2001004-29874', 2028, 2413.32, 'CONFIRMADA', '2022-07-08 20:47:51', '2022-07-30 04:33:12'),
(26, '62d19631be5b4', 8, 1, '2022-07-24', '2022-07-26', 'Alfredo', 'Durazno Martinez', 'durazno_619@hotmail.com', '7731140486', 'Card', 'Alfredo Durazno Martínez ', '4213166139738607', '04/26', '799', '', 'REALIZO DEPOSITO', 1157, 1376.83, 'CONFIRMADA', '2022-07-15 09:30:42', '2022-07-20 12:45:46'),
(27, '62d22ff854dc3', 8, 1, '2022-07-24', '2022-07-26', 'Alfredo', 'Durazno Martinez', 'durazno_619@hotmail.com', '7731140486', 'Card', 'Alfredo Durazno Martínez ', '4213166139738607', '04/26', '799', '', 'error de reserva', 1157, 1376.83, 'CANCELADA', '2022-07-15 20:26:48', '2022-07-18 14:32:34'),
(28, '62d2f78c72707', 8, 3, '2022-07-20', '2022-07-24', 'Melisa', 'Alvarado santiago ', 'meliconejo09@gmail.com ', '6647667679', 'Card', 'Melisa Alvarado Santiago ', '4915663414620774', '03/23', '911', '', 'sin disponibilidada', 3262.54, 3882.43, 'CANCELADA', '2022-07-16 10:38:21', '2022-07-18 13:07:30'),
(29, '62d343e9b6605', 9, 1, '2022-07-23', '2022-07-25', 'Mercedes ', 'Sanmiguel Vasquez ', 'mercedes.sanmiguel@ine.mx ', '3141220498', 'SPEI', 'Banamex ', '5482340151661697', '08/25', '', '', 'numero de confirmación del hotel:1997004-29949', 1482, 1763.58, 'CONFIRMADA', '2022-07-16 16:04:10', '2022-07-18 13:58:04'),
(30, '62d584a21bfa9', 8, 1, '2022-07-24', '2022-07-26', 'Alfredo', 'Durazno Martinez', 'durazno_619@hotmail.com', '7731140486', 'Card', 'Alfredo Durazno Martínez ', '4213166139738607', '04/26', '799', '', 'error de reserva', 1157, 1376.83, 'CANCELADA', '2022-07-18 09:04:50', '2022-07-18 14:32:47'),
(31, '62d61db90675d', 9, 1, '2022-07-19', '2022-07-24', 'Juan Gil', 'Costilla Cantu', 'gilcostilla.01@gmail.com', '8116659765', 'Card', 'Juan Gil Costilla Cantu ', '4915663090766115', '02/25', '884', '', 'NUMERO DE CONFIRMACION DE HOTEL: 1996004-29966', 3705, 4408.95, 'CONFIRMADA', '2022-07-18 19:58:01', '2022-07-19 08:04:25'),
(32, '62d97f7149852', 8, 1, '2022-07-24', '2022-07-26', 'Alfredo', 'Durazno Martinez', 'durazno_619@hotmail.com', '7731140486', 'Card', 'Alfredo Durazno Martínez ', '4213166139738607', '04/26', '799', '', 'REALIZO DEPOSITO', 1157, 1376.83, 'CONFIRMADA', '2022-07-21 09:31:45', '2022-07-22 08:23:59'),
(33, '62fbc5d17bf6d', 8, 1, '2022-09-26', '2022-09-27', 'Valeria Leticia ', 'Acosta Kawas', 'valeria.lak@gmail.com', '+50 4 31920264 ', 'Card', 'Valeria Acosta', '4210470400054196', '04/2023', '603', '', 'peticion del huesped', 676, 804.44, 'CANCELADA', '2022-08-16 09:29:06', '2022-08-17 10:52:40'),
(34, '62fbc64c45511', 8, 1, '2022-09-28', '2022-09-30', 'Valeria Leticia ', 'Acosta Kawas', 'valeria.lak@gmail.com', '+50 4 31920264 ', 'Card', 'Valeria Acosta', '4210470400054196', '04/2023', '603', '', 'peticion del huesped', 1352, 1608.88, 'CANCELADA', '2022-08-16 09:31:08', '2022-08-17 10:52:48'),
(35, '630bdababca53', 9, 1, '2022-09-05', '2022-09-06', 'Diana Camila', 'Botello Zepeda', 'ck_bottle@hotmail.com', '5528478228', 'Card', 'Norma Araceli Zepeda Ibarra', '4915733002648097', '01/26', '465', 'La estancia será del 5 al 11 de septiembre, el resto de las noches se pagará al llegar al hotel (5 días más).', 'PAGADA TOTALMENTE. POR EL HUESPED EN RECEPCION', 741, 881.79, 'CONFIRMADA', '2022-08-28 14:14:35', '2022-09-05 12:53:08'),
(36, '630ff83d1a012', 9, 2, '2022-09-13', '2022-09-21', 'Zurisadai', 'Arce Magaña', 'Zuri.arce18@gmail.com', '6641930683', 'SPEI', '', '', '/', '', '', 'sin disponibilidad', 7706.4, 9170.62, 'CANCELADA', '2022-08-31 17:09:33', '2022-09-08 06:50:40'),
(37, '63118ba026af9', 8, 1, '2022-09-02', '2022-09-08', 'Roberto', 'Reza', 'rreza@sisco-mx.com', '3389954860', 'Card', 'Roberto Reza', '37670605151102', '05/26', '7976', '', 'SIN DISPONIBILIDADA', 3471, 4130.49, 'CANCELADA', '2022-09-01 21:50:40', '2022-09-02 16:11:22'),
(38, '63176a0c24cab', 8, 1, '2022-09-09', '2022-09-10', 'Rodolfo', 'Garcia Pardo', 'garciarodolfoorto@gmail.com', '3251001464', 'Card', 'Rodolfo Noe Garcia Rivera ', '5532530010527559', '04/27', '440', '', 'GARANTIZADA', 676, 804.44, 'CONFIRMADA', '2022-09-06 08:41:01', '2022-09-06 10:19:02'),
(39, '63176a1c70406', 8, 1, '2022-09-09', '2022-09-10', 'Rodolfo', 'Garcia Pardo', 'garciarodolfoorto@gmail.com', '3251001464', 'Card', 'Rodolfo Noe Garcia Rivera ', '5532530010527559', '04/27', '440', '', 'DUPLICADA X HUESPED', 676, 804.44, 'CANCELADA', '2022-09-06 08:41:16', '2022-09-06 10:50:16'),
(40, '63178d979ee9e', 8, 1, '2022-09-08', '2022-09-10', 'José Luis ', 'Gámez Arroyo', 'drgamez0711@gmail.com', '3111342032', 'Card', 'José Luis Gámez Arroyo', '5482341209188584', '07/23', '626', '', 'numero de confirmacion del hotel: 2005004-31332', 1352, 1608.88, 'CONFIRMADA', '2022-09-06 11:12:40', '2022-09-08 06:30:30'),
(41, '6317aac6e236a', 8, 1, '2022-09-08', '2022-09-11', 'Francisco Adrian ', 'Enciso Rios ', 'faerguerra01@gmail.com', '3221929032', 'Card', 'Francisco Adrian Enciso Rios', '5256781179480996', '01/23', '753', 'Estaríamos llegando después de las 11pm.', '.', 2028, 2413.32, 'CONFIRMADA', '2022-09-06 13:17:11', '2022-09-08 06:20:23'),
(42, '6317d141cb3c6', 9, 1, '2022-09-09', '2022-09-10', 'Claudia', 'Morett Alatorre ', 'monarcamariposa@gmail.com', '4433571380', 'Card', 'Hugo Espinosa Chapa', '5549003003605508', '12/24', '056', 'Llegamos después de la 7pm\r\nPlanta baja, junto a la alberca', 'sin disponibilidad.', 741, 881.79, 'CANCELADA', '2022-09-06 16:01:22', '2022-09-08 06:34:24'),
(43, '6317ec03b8121', 9, 1, '2022-09-16', '2022-09-17', 'Miguel Isauro ', 'Martinez Marquez', 'lilautzom@hotmail.com', '442645758', 'Card', ' MIGUEL I MARTÍNEZ MARQUEZ', '376729189642002', '06/27', '8416', '', 'sin disponibilidad', 838.5, 997.815, 'CANCELADA', '2022-09-06 17:55:32', '2022-09-08 06:35:07'),
(44, '6317ed0566471', 9, 1, '2022-09-16', '2022-09-17', 'Lilia Laura', 'Tzompantzi Macias', 'lilautzom@hotmail.com', '4421864466', 'Card', 'LILIA L TZOMPANTZI M', '376729189641012', '05/27', '4991', '', 'sin disponibilidad', 741, 881.79, 'CANCELADA', '2022-09-06 17:59:49', '2022-09-08 06:35:26'),
(45, '631ab62ca1723', 8, 1, '2022-09-09', '2022-09-11', 'Jorge Luis', 'Contreras Barajas', 'joluiggi_20@hotmail.com', '3314054588', 'Card', 'J Luis Contreras Barajas', '5579070061331875', '08/26', '433', '', 'NUMERO DE CONFIRMACION DEL HOTEL:  2004004-31352', 1352, 1608.88, 'CONFIRMADA', '2022-09-08 20:42:37', '2022-09-09 08:59:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `default_capacity` int(10) UNSIGNED NOT NULL,
  `max_capacity` int(10) UNSIGNED NOT NULL,
  `image_cover` varchar(255) NOT NULL DEFAULT 'https://lorempixel.com/555/360/',
  `price` float UNSIGNED NOT NULL,
  `additional_guest` float NOT NULL,
  `availability` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rooms`
--

INSERT INTO `rooms` (`room_id`, `name`, `description`, `default_capacity`, `max_capacity`, `image_cover`, `price`, `additional_guest`, `availability`, `created_at`, `updated_at`) VALUES
(8, 'SENCILLA', '1 CAMA MATRIMONIAL', 1, 2, 'https://www.hotelisabel.com/es/img/room/habitacion-sencilla-hotel-isabel-gdl.png', 890, 150, 0, '2022-04-29 09:31:11', '2022-09-09 14:51:06'),
(9, 'DOBLE', '2 CAMAS MATRIMONIALES', 2, 4, 'https://www.hotelisabel.com/es/img/room/habitacion-doble-hotel-isabel-gdl.png', 990, 150, 0, '2022-04-29 09:34:33', '2022-09-09 14:50:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rooms_discounts`
--

CREATE TABLE `rooms_discounts` (
  `room_id` int(10) UNSIGNED NOT NULL,
  `discount_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rooms_discounts`
--

INSERT INTO `rooms_discounts` (`room_id`, `discount_id`) VALUES
(8, 1),
(9, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(15) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `pass`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sistemas PCBTroniks', 'sistemas', '$2y$12$gu5R62EJ0GHEsyPcm5Kw6es6HGxuLwo6gE40CRkNpdwmRDMyQlxyG', '1', '2021-02-12 14:14:38', '2021-02-12 14:14:38'),
(2, 'Ventas', 'ventas', '$2y$10$sKK.yD1qrUfHEgHNhdyHEe4SUUOIZAALrIqECXqScHnfBJjkSkQky', '1', '2022-01-20 08:28:54', '2022-04-29 13:42:16'),
(4, 'Juan Carlos', 'JC', '$2y$10$ibj1ic2ula1HfcEHixyvKeDtazHSrCJsfqJTNAr7qFgKCdsuNy8Iu', '1', '2022-04-29 13:42:41', '2022-04-29 13:42:41');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`),
  ADD UNIQUE KEY `discount_code` (`discount_code`);

--
-- Indices de la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `room_id` (`room_id`);

--
-- Indices de la tabla `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `rooms_discounts`
--
ALTER TABLE `rooms_discounts`
  ADD PRIMARY KEY (`room_id`,`discount_id`),
  ADD KEY `discount_id` (`discount_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `rooms_discounts`
--
ALTER TABLE `rooms_discounts`
  ADD CONSTRAINT `rooms_discounts_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rooms_discounts_ibfk_2` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
