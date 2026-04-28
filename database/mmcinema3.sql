-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-03-2026 a las 13:16:06
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mmcinema3`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `critica`
--

CREATE TABLE `critica` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_pelicula` int(11) DEFAULT NULL,
  `contenido` text NOT NULL,
  `puntuacion` tinyint(4) DEFAULT NULL COMMENT 'Valoración de 1 a 5',
  `creado` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `critica`
--

INSERT INTO `critica` (`id`, `id_usuario`, `id_pelicula`, `contenido`, `puntuacion`, `creado`) VALUES
(6, 8, 6, 'me encanta', 1, '2025-12-01 12:29:24'),
(7, 8, 1, 'mkifddd', 5, '2025-12-01 12:29:38'),
(9, 9, 20, 'top', 5, '2026-02-02 08:26:48'),
(10, 9, 20, 'sdfs', 2, '2026-02-02 08:27:00'),
(11, 9, 16, 'hfbfsdb f', 4, '2026-02-02 08:34:56'),
(12, 9, 16, 'sdvfds', 1, '2026-02-02 08:35:12'),
(13, 9, 16, 'sdfd', 4, '2026-02-02 08:52:29'),
(14, 11, 10, 'top', 4, '2026-03-09 08:34:29'),
(15, 8, 21, 'dfikf', 2, '2026-03-23 08:17:54'),
(16, 8, 21, 'fdsf', 1, '2026-03-23 08:27:16'),
(17, 1, 22, 'rfe', 5, '2026-03-23 11:28:38'),
(18, 8, 4, 'wq', 4, '2026-03-23 12:42:18'),
(19, 17, 22, 'cv', 2, '2026-03-24 08:56:01'),
(20, 17, 4, 'sds', 1, '2026-03-24 11:21:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `critica_serie`
--

CREATE TABLE `critica_serie` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `puntuacion` tinyint(4) NOT NULL,
  `creado` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Volcado de datos para la tabla `critica_serie`
--

INSERT INTO `critica_serie` (`id`, `id_usuario`, `id_serie`, `contenido`, `puntuacion`, `creado`) VALUES
(1, 17, 2, 'dsfds', 2, '2026-03-25 09:50:02'),
(2, 17, 10, 'fdf', 3, '2026-03-26 09:26:40'),
(3, 17, 12, 'vdf', 5, '2026-03-26 08:15:37'),
(4, 17, 4, 's', 1, '2026-03-26 09:29:11'),
(5, 17, 18, 'swd', 5, '2026-03-26 09:29:23'),
(6, 17, 9, 'dv', 4, '2026-03-26 09:29:38'),
(7, 17, 21, 'fdgv', 4, '2026-03-26 12:14:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `episodio`
--

CREATE TABLE `episodio` (
  `id` int(11) NOT NULL,
  `id_temporada` int(11) NOT NULL,
  `numero_episodio` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `fecha_estreno` date DEFAULT NULL,
  `creado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `episodio`
--

INSERT INTO `episodio` (`id`, `id_temporada`, `numero_episodio`, `titulo`, `descripcion`, `duracion`, `fecha_estreno`, `creado`) VALUES
(1, 1, 1, 'La desaparición de Will Byers', 'Un niño desaparece y el pueblo entra en caos.', 49, '2016-07-15', '2026-03-25 08:17:10'),
(2, 1, 2, 'La loca de Maple Street', 'Los chicos comienzan a investigar lo ocurrido.', 55, '2016-07-15', '2026-03-25 08:17:10'),
(3, 2, 1, 'MADMAX', 'La normalidad vuelve, pero no por mucho tiempo.', 48, '2017-10-27', '2026-03-25 08:17:10'),
(4, 3, 1, 'Cuando estés perdido en la oscuridad', 'Joel afronta el colapso del mundo.', 81, '2023-01-15', '2026-03-25 08:17:10'),
(5, 4, 1, 'The Name of the Game', 'Hughie descubre la verdad sobre los supes.', 57, '2019-07-26', '2026-03-25 08:17:10'),
(6, 5, 1, 'Piloto', 'Tony Soprano comienza terapia mientras intenta manejar su vida familiar y criminal.', 60, '1999-01-10', '2026-03-26 07:53:14'),
(7, 6, 1, 'Guy Walks into a Psychiatrist\'s Office', 'Tony regresa a terapia mientras nuevas tensiones aparecen en su entorno.', 50, '2000-01-16', '2026-03-26 07:53:41'),
(8, 7, 1, 'System', 'Carmy llega al restaurante y descubre el caos que dejó su hermano.', 30, '2022-06-23', '2026-03-26 07:58:17'),
(9, 8, 1, 'Beef', 'El equipo empieza el proceso para transformar el local en algo totalmente nuevo.', 31, '2023-06-22', '2026-03-26 07:58:46'),
(10, 11, 1, 'Winter Is Coming', 'Eddard Stark recibe la visita del rey Robert Baratheon y se ve envuelto en el juego político.', 62, '2011-04-17', '2026-03-26 08:09:17'),
(11, 11, 2, 'The Kingsroad', 'Los Stark y los Lannister sufren las consecuencias de un altercado en el camino del rey.', 56, '2011-04-24', '2026-03-26 08:09:17'),
(12, 11, 3, 'Lord Snow', 'Jon Nieve llega al Muro mientras Ned empieza a descubrir secretos en Desembarco del Rey.', 58, '2011-05-01', '2026-03-26 08:09:17'),
(13, 11, 4, 'Cripples, Bastards, and Broken Things', 'Ned investiga la muerte de Jon Arryn y Tyrion regresa del Muro.', 56, '2011-05-08', '2026-03-26 08:09:17'),
(14, 11, 5, 'The Wolf and the Lion', 'Las tensiones entre Stark y Lannister estallan tras nuevos descubrimientos.', 55, '2011-05-15', '2026-03-26 08:09:17'),
(15, 11, 6, 'A Golden Crown', 'Viserys lleva su ambición demasiado lejos mientras Ned se enfrenta a la corona.', 53, '2011-05-22', '2026-03-26 08:09:17'),
(16, 11, 7, 'You Win or You Die', 'Robert queda fuera del juego y Ned debe decidir su próximo movimiento.', 59, '2011-05-29', '2026-03-26 08:09:17'),
(17, 11, 8, 'The Pointy End', 'Tras la caída de Ned, Arya huye y los Stark se preparan para la guerra.', 59, '2011-06-05', '2026-03-26 08:09:17'),
(18, 11, 9, 'Baelor', 'Un juicio cambia para siempre el destino de los Stark y de Poniente.', 57, '2011-06-12', '2026-03-26 08:09:17'),
(19, 11, 10, 'Fire and Blood', 'El norte se alza, Jon toma una decisión y Daenerys entra en el fuego.', 53, '2011-06-19', '2026-03-26 08:09:17'),
(20, 12, 1, 'The North Remembers', 'La guerra se extiende por los Siete Reinos y Tyrion llega a Desembarco del Rey.', 53, '2012-04-01', '2026-03-26 08:09:17'),
(21, 12, 2, 'The Night Lands', 'Arya sigue huyendo y Theon regresa a las Islas del Hierro.', 54, '2012-04-08', '2026-03-26 08:09:17'),
(22, 12, 3, 'What Is Dead May Never Die', 'Tyrion maniobra en la corte y Catelyn busca alianzas.', 53, '2012-04-15', '2026-03-26 08:09:17'),
(23, 12, 4, 'Garden of Bones', 'Joffrey muestra su crueldad mientras Daenerys busca apoyo en Qarth.', 51, '2012-04-22', '2026-03-26 08:09:17'),
(24, 12, 5, 'The Ghost of Harrenhal', 'Arya llega a Harrenhal y Renly afronta su destino.', 55, '2012-04-29', '2026-03-26 08:09:17'),
(25, 12, 6, 'The Old Gods and the New', 'Theon toma Invernalia y Jon se adentra más allá del Muro.', 54, '2012-05-06', '2026-03-26 08:09:17'),
(26, 12, 7, 'A Man Without Honor', 'Jaime intenta escapar y Theon se hunde en sus decisiones.', 56, '2012-05-13', '2026-03-26 08:09:17'),
(27, 12, 8, 'The Prince of Winterfell', 'Stannis se aproxima a Desembarco del Rey y Daenerys sufre una traición.', 54, '2012-05-20', '2026-03-26 08:09:17'),
(28, 12, 9, 'Blackwater', 'La batalla de Aguasnegras decide el destino de la capital.', 55, '2012-05-27', '2026-03-26 08:09:17'),
(29, 12, 10, 'Valar Morghulis', 'Los supervivientes afrontan las consecuencias de la batalla y nuevas amenazas aparecen.', 63, '2012-06-03', '2026-03-26 08:09:17'),
(30, 13, 1, 'Valar Dohaeris', 'Jon es llevado ante Mance Rayder y Daenerys comienza a reunir ejército.', 54, '2013-03-31', '2026-03-26 08:09:18'),
(31, 13, 2, 'Dark Wings, Dark Words', 'Bran tiene nuevas visiones y Jaime viaja con Brienne.', 57, '2013-04-07', '2026-03-26 08:09:18'),
(32, 13, 3, 'Walk of Punishment', 'Jaime y Brienne sufren un ataque y Tyrion asume nuevas responsabilidades.', 53, '2013-04-14', '2026-03-26 08:09:18'),
(33, 13, 4, 'And Now His Watch Is Ended', 'Daenerys toma una decisión decisiva en Astapor.', 55, '2013-04-21', '2026-03-26 08:09:18'),
(34, 13, 5, 'Kissed by Fire', 'Jon y Ygritte estrechan su relación y Jaime pierde una parte de sí mismo.', 58, '2013-04-28', '2026-03-26 08:09:18'),
(35, 13, 6, 'The Climb', 'Se preparan planes arriesgados en varios frentes de la guerra.', 54, '2013-05-05', '2026-03-26 08:09:18'),
(36, 13, 7, 'The Bear and the Maiden Fair', 'Jon demuestra su lealtad y Daenerys se acerca a Yunkai.', 58, '2013-05-12', '2026-03-26 08:09:18'),
(37, 13, 8, 'Second Sons', 'Daenerys negocia con mercenarios y Sam protege a Gilly.', 56, '2013-05-19', '2026-03-26 08:09:18'),
(38, 13, 9, 'The Rains of Castamere', 'Una boda cambia el rumbo de la guerra de la forma más brutal.', 51, '2013-06-02', '2026-03-26 08:09:18'),
(39, 13, 10, 'Mhysa', 'Tras las pérdidas, cada bando reorganiza su futuro.', 63, '2013-06-09', '2026-03-26 08:09:18'),
(40, 14, 1, 'Two Swords', 'Tywin refuerza el poder de su casa mientras el norte sigue dividido.', 58, '2014-04-06', '2026-03-26 08:09:18'),
(41, 14, 2, 'The Lion and the Rose', 'Una boda real toma un giro inesperado y mortal.', 52, '2014-04-13', '2026-03-26 08:09:18'),
(42, 14, 3, 'Breaker of Chains', 'Tyrion es acusado y Daenerys continúa su campaña de liberación.', 57, '2014-04-20', '2026-03-26 08:09:18'),
(43, 14, 4, 'Oathkeeper', 'Jaime da un nuevo propósito a Brienne y Jon alerta del peligro salvaje.', 55, '2014-04-27', '2026-03-26 08:09:18'),
(44, 14, 5, 'First of His Name', 'Tommen es coronado y los conflictos familiares se agravan.', 53, '2014-05-04', '2026-03-26 08:09:18'),
(45, 14, 6, 'The Laws of Gods and Men', 'Tyrion es juzgado y Stannis valora su próximo movimiento.', 51, '2014-05-11', '2026-03-26 08:09:18'),
(46, 14, 7, 'Mockingbird', 'Tyrion busca un campeón y Arya sigue su viaje con El Perro.', 51, '2014-05-18', '2026-03-26 08:09:18'),
(47, 14, 8, 'The Mountain and the Viper', 'El juicio por combate enfrenta a dos guerreros inolvidables.', 52, '2014-06-01', '2026-03-26 08:09:18'),
(48, 14, 9, 'The Watchers on the Wall', 'El Muro resiste un asalto salvaje en una batalla decisiva.', 50, '2014-06-08', '2026-03-26 08:09:18'),
(49, 14, 10, 'The Children', 'Varios destinos se cruzan y algunos personajes emprenden nuevos caminos.', 66, '2014-06-15', '2026-03-26 08:09:18'),
(50, 15, 1, 'The Wars to Come', 'Cersei afronta un nuevo escenario político y Daenerys intenta gobernar Meereen.', 53, '2015-04-12', '2026-03-26 08:09:18'),
(51, 15, 2, 'The House of Black and White', 'Arya llega a Braavos y Jon debe tomar decisiones difíciles.', 54, '2015-04-19', '2026-03-26 08:09:18'),
(52, 15, 3, 'High Sparrow', 'Tommen y Margaery consolidan su posición mientras crece una nueva fe.', 60, '2015-04-26', '2026-03-26 08:09:18'),
(53, 15, 4, 'Sons of the Harpy', 'Daenerys sufre una ofensiva en Meereen y Jon hace un trato arriesgado.', 51, '2015-05-03', '2026-03-26 08:09:18'),
(54, 15, 5, 'Kill the Boy', 'Jon intenta unir fuerzas más allá del Muro y Daenerys escucha consejos.', 57, '2015-05-10', '2026-03-26 08:09:18'),
(55, 15, 6, 'Unbowed, Unbent, Unbroken', 'Sansa llega a Invernalia y Arya avanza en su entrenamiento.', 54, '2015-05-17', '2026-03-26 08:09:18'),
(56, 15, 7, 'The Gift', 'Jon se reúne con los salvajes y Cersei pierde parte de su control.', 59, '2015-05-24', '2026-03-26 08:09:18'),
(57, 15, 8, 'Hardhome', 'Jon afronta una amenaza enorme en Casa Austera.', 60, '2015-05-31', '2026-03-26 08:09:18'),
(58, 15, 9, 'The Dance of Dragons', 'Stannis se prepara para la guerra y Daenerys participa en una gran celebración.', 53, '2015-06-07', '2026-03-26 08:09:18'),
(59, 15, 10, 'Mother\'s Mercy', 'Las consecuencias de la temporada golpean a todos los bandos.', 60, '2015-06-14', '2026-03-26 08:09:18'),
(60, 16, 1, 'The Red Woman', 'Tras los sucesos del Muro, el futuro de Jon queda en duda.', 50, '2016-04-24', '2026-03-26 08:09:18'),
(61, 16, 2, 'Home', 'Nuevos liderazgos surgen y algunas esperanzas renacen.', 54, '2016-05-01', '2026-03-26 08:09:18'),
(62, 16, 3, 'Oathbreaker', 'Bran sigue sus visiones y Daenerys afronta el destino de los dothraki.', 52, '2016-05-08', '2026-03-26 08:09:18'),
(63, 16, 4, 'Book of the Stranger', 'Sansa y Jon se reencuentran mientras Daenerys recupera poder.', 59, '2016-05-15', '2026-03-26 08:09:18'),
(64, 16, 5, 'The Door', 'Bran descubre el verdadero origen de Hodor.', 58, '2016-05-22', '2026-03-26 08:09:18'),
(65, 16, 6, 'Blood of My Blood', 'Benjen reaparece y Sam vuelve con su familia.', 52, '2016-05-29', '2026-03-26 08:09:18'),
(66, 16, 7, 'The Broken Man', 'El Perro regresa y las tensiones siguen creciendo en varios frentes.', 51, '2016-06-05', '2026-03-26 08:09:18'),
(67, 16, 8, 'No One', 'Arya toma una decisión final sobre su identidad.', 59, '2016-06-12', '2026-03-26 08:09:18'),
(68, 16, 9, 'Battle of the Bastards', 'La lucha por Invernalia deja una de las grandes batallas de la serie.', 60, '2016-06-19', '2026-03-26 08:09:18'),
(69, 16, 10, 'The Winds of Winter', 'Los grandes jugadores mueven sus últimas piezas antes de la guerra final.', 69, '2016-06-26', '2026-03-26 08:09:18'),
(70, 17, 1, 'Dragonstone', 'Daenerys llega finalmente a Rocadragón y los bandos se posicionan.', 59, '2017-07-16', '2026-03-26 08:09:18'),
(71, 17, 2, 'Stormborn', 'Daenerys reúne a sus aliados y Jon recibe noticias inquietantes.', 59, '2017-07-23', '2026-03-26 08:09:18'),
(72, 17, 3, 'The Queen\'s Justice', 'Las alianzas se redefinen y se producen encuentros muy esperados.', 63, '2017-07-30', '2026-03-26 08:09:18'),
(73, 17, 4, 'The Spoils of War', 'Una batalla inesperada cambia el equilibrio entre los bandos.', 50, '2017-08-06', '2026-03-26 08:09:18'),
(74, 17, 5, 'Eastwatch', 'Se planea una expedición desesperada más allá del Muro.', 59, '2017-08-13', '2026-03-26 08:09:18'),
(75, 17, 6, 'Beyond the Wall', 'Jon y su grupo se enfrentan al Rey de la Noche en el norte helado.', 71, '2017-08-20', '2026-03-26 08:09:18'),
(76, 17, 7, 'The Dragon and the Wolf', 'Los líderes de Poniente se reúnen para decidir el futuro del reino.', 79, '2017-08-27', '2026-03-26 08:09:18'),
(77, 18, 1, 'Winterfell', 'Los protagonistas se reúnen en Invernalia mientras la guerra definitiva se acerca.', 54, '2019-04-14', '2026-03-26 08:09:18'),
(78, 18, 2, 'A Knight of the Seven Kingdoms', 'Antes de la batalla, los personajes comparten confesiones y despedidas.', 58, '2019-04-21', '2026-03-26 08:09:18'),
(79, 18, 3, 'The Long Night', 'La batalla contra los caminantes blancos decide el destino del norte.', 82, '2019-04-28', '2026-03-26 08:09:18'),
(80, 18, 4, 'The Last of the Starks', 'Tras la gran batalla, nuevas divisiones surgen entre los vencedores.', 78, '2019-05-05', '2026-03-26 08:09:18'),
(81, 18, 5, 'The Bells', 'El asalto a Desembarco del Rey cambia para siempre la historia de Poniente.', 80, '2019-05-12', '2026-03-26 08:09:18'),
(82, 18, 6, 'The Iron Throne', 'La guerra termina y se decide quién gobernará el futuro de los Siete Reinos.', 80, '2019-05-19', '2026-03-26 08:09:18'),
(208, 34, 1, 'Pilot', 'Walter White decide cambiar su vida para siempre.', 60, '2008-01-20', '2026-03-26 09:12:49'),
(209, 34, 2, 'Cat\'s in the Bag...', 'Walter y Jesse intentan cubrir sus huellas.', 60, '2008-01-27', '2026-03-26 09:12:49'),
(210, 34, 3, '...and the Bag\'s in the River', 'Walter se enfrenta a su primera decisión extrema.', 60, '2008-02-10', '2026-03-26 09:12:49'),
(211, 34, 4, 'Cancer Man', 'La noticia de Walter afecta a toda la familia.', 60, '2008-02-17', '2026-03-26 09:12:49'),
(212, 34, 5, 'Gray Matter', 'Walter recibe una oferta que remueve su pasado.', 60, '2008-02-24', '2026-03-26 09:12:49'),
(213, 34, 6, 'Crazy Handful of Nothin\'', 'Walter empieza a transformarse en Heisenberg.', 60, '2008-03-02', '2026-03-26 09:12:49'),
(214, 34, 7, 'A No-Rough-Stuff-Type Deal', 'El negocio se complica con nuevos riesgos.', 60, '2008-03-09', '2026-03-26 09:12:49'),
(215, 35, 1, 'Seven Thirty-Seven', 'Las consecuencias del enfrentamiento obligan a replantearlo todo.', 60, '2009-03-08', '2026-03-26 09:12:49'),
(216, 35, 2, 'Grilled', 'Walter y Jesse viven una situación límite.', 60, '2009-03-15', '2026-03-26 09:12:49'),
(217, 35, 3, 'Bit by a Dead Bee', 'Walter intenta mantener su doble vida.', 60, '2009-03-22', '2026-03-26 09:12:49'),
(218, 35, 4, 'Down', 'La tensión entre Walter y Jesse va en aumento.', 60, '2009-03-29', '2026-03-26 09:12:49'),
(219, 35, 5, 'Breakage', 'El negocio vuelve a ponerse en marcha.', 60, '2009-04-05', '2026-03-26 09:12:49'),
(220, 35, 6, 'Peekaboo', 'Jesse se enfrenta a una realidad devastadora.', 60, '2009-04-12', '2026-03-26 09:12:49'),
(221, 35, 7, 'Negro Y Azul', 'La expansión del negocio atrae nueva atención.', 60, '2009-04-19', '2026-03-26 09:12:49'),
(222, 35, 8, 'Better Call Saul', 'Aparece un abogado clave para el futuro de Walter y Jesse.', 60, '2009-04-26', '2026-03-26 09:12:49'),
(223, 35, 9, '4 Days Out', 'Una salida al desierto se convierte en una prueba extrema.', 60, '2009-05-03', '2026-03-26 09:12:49'),
(224, 35, 10, 'Over', 'Walter empieza a perder el control de su vida familiar.', 60, '2009-05-10', '2026-03-26 09:12:49'),
(225, 35, 11, 'Mandala', 'Un nuevo giro cambia la escala del negocio.', 60, '2009-05-17', '2026-03-26 09:12:49'),
(226, 35, 12, 'Phoenix', 'Las decisiones personales tienen consecuencias devastadoras.', 60, '2009-05-24', '2026-03-26 09:12:49'),
(227, 35, 13, 'ABQ', 'Todo desemboca en un desenlace trágico.', 60, '2009-05-31', '2026-03-26 09:12:49'),
(228, 36, 1, 'No Más', 'Walter intenta alejarse del negocio.', 60, '2010-03-21', '2026-03-26 09:12:49'),
(229, 36, 2, 'Caballo sin Nombre', 'Las tensiones familiares y criminales se mezclan.', 60, '2010-03-28', '2026-03-26 09:12:49'),
(230, 36, 3, 'I.F.T.', 'La vida personal de Walter se desmorona.', 60, '2010-04-04', '2026-03-26 09:12:49'),
(231, 36, 4, 'Green Light', 'Walter se siente desplazado y toma decisiones arriesgadas.', 60, '2010-04-11', '2026-03-26 09:12:49'),
(232, 36, 5, 'Más', 'Walter vuelve a cruzar una línea peligrosa.', 60, '2010-04-18', '2026-03-26 09:12:49'),
(233, 36, 6, 'Sunset', 'Hank avanza en su investigación.', 60, '2010-04-25', '2026-03-26 09:12:49'),
(234, 36, 7, 'One Minute', 'La tensión llega a un punto de ruptura.', 60, '2010-05-02', '2026-03-26 09:12:49'),
(235, 36, 8, 'I See You', 'Las consecuencias del ataque sacuden a todos.', 60, '2010-05-09', '2026-03-26 09:12:49'),
(236, 36, 9, 'Kafkaesque', 'Jesse y Walter encuentran nuevas oportunidades.', 60, '2010-05-16', '2026-03-26 09:12:49'),
(237, 36, 10, 'Fly', 'Walter queda atrapado con sus pensamientos y sus culpas.', 60, '2010-05-23', '2026-03-26 09:12:49'),
(238, 36, 11, 'Abiquiu', 'Skyler entra más a fondo en el secreto de Walter.', 60, '2010-05-30', '2026-03-26 09:12:49'),
(239, 36, 12, 'Half Measures', 'Walter toma una decisión clave para proteger a Jesse.', 60, '2010-06-06', '2026-03-26 09:12:49'),
(240, 36, 13, 'Full Measure', 'El conflicto llega a un final explosivo.', 60, '2010-06-13', '2026-03-26 09:12:49'),
(241, 37, 1, 'Box Cutter', 'Tras el caos, Gus impone su autoridad.', 60, '2011-07-17', '2026-03-26 09:12:49'),
(242, 37, 2, 'Thirty-Eight Snub', 'Walter intenta ganar ventaja frente a Gus.', 60, '2011-07-24', '2026-03-26 09:12:49'),
(243, 37, 3, 'Open House', 'La tensión se traslada también al ámbito personal.', 60, '2011-07-31', '2026-03-26 09:12:49'),
(244, 37, 4, 'Bullet Points', 'Hank sigue más cerca que nunca de la verdad.', 60, '2011-08-07', '2026-03-26 09:12:49'),
(245, 37, 5, 'Shotgun', 'Jesse recibe una nueva misión.', 60, '2011-08-14', '2026-03-26 09:12:49'),
(246, 37, 6, 'Cornered', 'Walter se reafirma en su nueva identidad.', 60, '2011-08-21', '2026-03-26 09:12:49'),
(247, 37, 7, 'Problem Dog', 'Jesse lidia con las consecuencias de sus actos.', 60, '2011-08-28', '2026-03-26 09:12:49'),
(248, 37, 8, 'Hermanos', 'El pasado de Gus revela nuevas claves.', 60, '2011-09-04', '2026-03-26 09:12:49'),
(249, 37, 9, 'Bug', 'Walter se obsesiona con los riesgos que lo rodean.', 60, '2011-09-11', '2026-03-26 09:12:49'),
(250, 37, 10, 'Salud', 'La presión aumenta en todos los frentes.', 60, '2011-09-18', '2026-03-26 09:12:49'),
(251, 37, 11, 'Crawl Space', 'Walter entra en pánico ante un futuro incontrolable.', 60, '2011-09-25', '2026-03-26 09:12:49'),
(252, 37, 12, 'End Times', 'Todo parece apuntar a un desenlace inevitable.', 60, '2011-10-02', '2026-03-26 09:12:49'),
(253, 37, 13, 'Face Off', 'La guerra con Gus alcanza su clímax.', 60, '2011-10-09', '2026-03-26 09:12:49'),
(254, 38, 1, 'Live Free or Die', 'Walter empieza una nueva fase tras eliminar a su mayor rival.', 60, '2012-07-15', '2026-03-26 09:12:49'),
(255, 38, 2, 'Madrigal', 'El negocio necesita reconstruirse desde cero.', 60, '2012-07-22', '2026-03-26 09:12:49'),
(256, 38, 3, 'Hazard Pay', 'La operación vuelve a ponerse en marcha.', 60, '2012-07-29', '2026-03-26 09:12:49'),
(257, 38, 4, 'Fifty-One', 'Walter celebra su cumpleaños con nuevas tensiones familiares.', 60, '2012-08-05', '2026-03-26 09:12:49'),
(258, 38, 5, 'Dead Freight', 'Un plan arriesgado pone a prueba a todo el equipo.', 60, '2012-08-12', '2026-03-26 09:12:49'),
(259, 38, 6, 'Buyout', 'Las prioridades de Walter se separan de las de Jesse y Mike.', 60, '2012-08-19', '2026-03-26 09:12:49'),
(260, 38, 7, 'Say My Name', 'Walter exige reconocimiento y respeto.', 60, '2012-08-26', '2026-03-26 09:12:49'),
(261, 38, 8, 'Gliding Over All', 'Walter parece haber ganado, pero el precio es enorme.', 60, '2012-09-02', '2026-03-26 09:12:49'),
(262, 38, 9, 'Blood Money', 'El pasado vuelve con fuerza.', 60, '2013-08-11', '2026-03-26 09:12:49'),
(263, 38, 10, 'Buried', 'Las mentiras ya no bastan para sostenerlo todo.', 60, '2013-08-18', '2026-03-26 09:12:49'),
(264, 38, 11, 'Confessions', 'La batalla entre Walter y Hank entra en una nueva fase.', 60, '2013-08-25', '2026-03-26 09:12:49'),
(265, 38, 12, 'Rabid Dog', 'El círculo se cierra cada vez más alrededor de Walter.', 60, '2013-09-01', '2026-03-26 09:12:49'),
(266, 38, 13, 'To\'hajiilee', 'Una trampa puede cambiarlo todo.', 60, '2013-09-08', '2026-03-26 09:12:49'),
(267, 38, 14, 'Ozymandias', 'La caída de Walter alcanza su punto más devastador.', 60, '2013-09-15', '2026-03-26 09:12:49'),
(268, 38, 15, 'Granite State', 'Walter afronta el aislamiento y la derrota.', 60, '2013-09-22', '2026-03-26 09:12:49'),
(269, 38, 16, 'Felina', 'La historia de Heisenberg llega a su final.', 60, '2013-09-29', '2026-03-26 09:12:49'),
(270, 39, 1, 'Uno', 'Jimmy McGill intenta sobrevivir como abogado de oficio.', 60, '2015-02-08', '2026-03-26 09:12:49'),
(271, 39, 2, 'Mijo', 'Jimmy se cruza con personajes peligrosos del submundo criminal.', 60, '2015-02-09', '2026-03-26 09:12:49'),
(272, 39, 3, 'Nacho', 'Jimmy recibe una oportunidad arriesgada.', 60, '2015-02-16', '2026-03-26 09:12:49'),
(273, 39, 4, 'Hero', 'Jimmy descubre el poder de la publicidad y de la imagen.', 60, '2015-02-23', '2026-03-26 09:12:49'),
(274, 39, 5, 'Alpine Shepherd Boy', 'Jimmy intenta redirigir su carrera con nuevos clientes.', 60, '2015-03-02', '2026-03-26 09:12:49'),
(275, 39, 6, 'Five-O', 'Mike Ehrmantraut muestra parte de su pasado.', 60, '2015-03-09', '2026-03-26 09:12:49'),
(276, 39, 7, 'Bingo', 'Jimmy saborea un pequeño triunfo profesional.', 60, '2015-03-16', '2026-03-26 09:12:49'),
(277, 39, 8, 'Rico', 'Una gran oportunidad legal aparece ante Jimmy.', 60, '2015-03-23', '2026-03-26 09:12:49'),
(278, 39, 9, 'Pimento', 'Jimmy debe decidir entre el prestigio y su propia naturaleza.', 60, '2015-03-30', '2026-03-26 09:12:49'),
(279, 39, 10, 'Marco', 'Jimmy mira atrás antes de abrazar el cambio.', 60, '2015-04-06', '2026-03-26 09:12:49'),
(280, 40, 1, 'Switch', 'Jimmy arranca una nueva etapa profesional.', 60, '2016-02-15', '2026-03-26 09:12:49'),
(281, 40, 2, 'Cobbler', 'Un caso complicado pone a prueba a Jimmy y Kim.', 60, '2016-02-22', '2026-03-26 09:12:49'),
(282, 40, 3, 'Amarillo', 'Jimmy vuelve a apostar por su estilo propio.', 60, '2016-02-29', '2026-03-26 09:12:49'),
(283, 40, 4, 'Gloves Off', 'Mike empieza a actuar contra nuevos enemigos.', 60, '2016-03-07', '2026-03-26 09:12:49'),
(284, 40, 5, 'Rebecca', 'El pasado de Chuck ayuda a entender su presente.', 60, '2016-03-14', '2026-03-26 09:12:49'),
(285, 40, 6, 'Bali Ha\'i', 'Jimmy intenta acercarse a Kim mientras el peligro crece.', 60, '2016-03-21', '2026-03-26 09:12:49'),
(286, 40, 7, 'Inflatable', 'Jimmy vuelve a las andadas.', 60, '2016-03-28', '2026-03-26 09:12:49'),
(287, 40, 8, 'Fifi', 'Mike prepara una operación meticulosa.', 60, '2016-04-04', '2026-03-26 09:12:49'),
(288, 40, 9, 'Nailed', 'La rivalidad entre los hermanos McGill se vuelve abierta.', 60, '2016-04-11', '2026-03-26 09:12:49'),
(289, 40, 10, 'Klick', 'Chuck toma una decisión decisiva contra Jimmy.', 60, '2016-04-18', '2026-03-26 09:12:49'),
(290, 41, 1, 'Mabel', 'Jimmy afronta las consecuencias de sus actos.', 70, '2017-04-10', '2026-03-26 09:12:49'),
(291, 41, 2, 'Witness', 'Mike y Gus cruzan caminos de forma más clara.', 70, '2017-04-17', '2026-03-26 09:12:49'),
(292, 41, 3, 'Sunk Costs', 'Jimmy busca una salida legal a su situación.', 70, '2017-04-24', '2026-03-26 09:12:49'),
(293, 41, 4, 'Sabrosito', 'Gus y Hector entran en conflicto abierto.', 70, '2017-05-01', '2026-03-26 09:12:49'),
(294, 41, 5, 'Chicanery', 'El enfrentamiento entre Jimmy y Chuck estalla en público.', 70, '2017-05-08', '2026-03-26 09:12:49'),
(295, 41, 6, 'Off Brand', 'Jimmy busca reinventarse a su manera.', 70, '2017-05-15', '2026-03-26 09:12:49'),
(296, 41, 7, 'Expenses', 'La suspensión de Jimmy afecta a todos los aspectos de su vida.', 70, '2017-05-22', '2026-03-26 09:12:49'),
(297, 41, 8, 'Slip', 'Jimmy y Kim intentan consolidar su nueva rutina.', 70, '2017-06-05', '2026-03-26 09:12:49'),
(298, 41, 9, 'Fall', 'Las decisiones de Jimmy y Chuck empujan la historia hacia un punto límite.', 77, '2017-06-12', '2026-03-26 09:12:49'),
(299, 41, 10, 'Lantern', 'Un final devastador marca el cierre de la temporada.', 76, '2017-06-19', '2026-03-26 09:12:49'),
(300, 42, 1, 'Smoke', 'Jimmy intenta seguir adelante tras una pérdida irreparable.', 60, '2018-08-06', '2026-03-26 09:12:49'),
(301, 42, 2, 'Breathe', 'Kim y Jimmy afrontan su nueva realidad.', 60, '2018-08-13', '2026-03-26 09:12:49'),
(302, 42, 3, 'Something Beautiful', 'Mike se adentra más en la organización de Gus.', 60, '2018-08-20', '2026-03-26 09:12:49'),
(303, 42, 4, 'Talk', 'Jimmy se adapta a un trabajo inesperado.', 60, '2018-08-27', '2026-03-26 09:12:49'),
(304, 42, 5, 'Quite a Ride', 'El futuro de Saul Goodman deja nuevas pistas.', 60, '2018-09-03', '2026-03-26 09:12:49'),
(305, 42, 6, 'Piñata', 'Jimmy vuelve a probar suerte con sus viejos métodos.', 60, '2018-09-10', '2026-03-26 09:12:49'),
(306, 42, 7, 'Something Stupid', 'La relación entre Kim y Jimmy cambia.', 60, '2018-09-17', '2026-03-26 09:12:49'),
(307, 42, 8, 'Coushatta', 'Jimmy idea un plan creativo para ayudar a Huell.', 60, '2018-09-24', '2026-03-26 09:12:49'),
(308, 42, 9, 'Wiedersehen', 'Todos los personajes se acercan a un cambio importante.', 60, '2018-10-01', '2026-03-26 09:12:49'),
(309, 42, 10, 'Winner', 'Jimmy da un paso definitivo hacia Saul Goodman.', 60, '2018-10-08', '2026-03-26 09:12:49'),
(310, 43, 1, 'Magic Man', 'Saul Goodman empieza a funcionar como marca.', 75, '2020-02-23', '2026-03-26 09:12:49'),
(311, 43, 2, '50% Off', 'Jimmy disfruta de su nueva libertad como Saul.', 60, '2020-02-24', '2026-03-26 09:12:49'),
(312, 43, 3, 'The Guy for This', 'Nacho se hunde más en un juego peligroso.', 75, '2020-03-02', '2026-03-26 09:12:49'),
(313, 43, 4, 'Namaste', 'Howard y Jimmy vuelven a cruzar sus caminos.', 60, '2020-03-09', '2026-03-26 09:12:49'),
(314, 43, 5, 'Dedicado a Max', 'Mike y Gus refuerzan su alianza.', 70, '2020-03-16', '2026-03-26 09:12:49'),
(315, 43, 6, 'Wexler v. Goodman', 'Kim se enfrenta a la parte más incómoda de Jimmy.', 70, '2020-03-23', '2026-03-26 09:12:49'),
(316, 43, 7, 'JMM', 'El trabajo de Saul lo mete en nuevos problemas.', 68, '2020-03-30', '2026-03-26 09:12:49'),
(317, 43, 8, 'Bagman', 'Jimmy atraviesa una experiencia extrema en el desierto.', 75, '2020-04-06', '2026-03-26 09:12:49'),
(318, 43, 9, 'Bad Choice Road', 'Kim y Jimmy afrontan las secuelas del desierto.', 85, '2020-04-13', '2026-03-26 09:12:49'),
(319, 43, 10, 'Something Unforgivable', 'La temporada cierra con un giro decisivo.', 85, '2020-04-20', '2026-03-26 09:12:49'),
(320, 44, 1, 'Wine and Roses', 'Jimmy y Kim avanzan con su plan contra Howard.', 60, '2022-04-18', '2026-03-26 09:12:49'),
(321, 44, 2, 'Carrot and Stick', 'El juego se vuelve más peligroso para todos.', 60, '2022-04-18', '2026-03-26 09:12:49'),
(322, 44, 3, 'Rock and Hard Place', 'Nacho se enfrenta a una situación sin salida.', 60, '2022-04-25', '2026-03-26 09:12:49'),
(323, 44, 4, 'Hit and Run', 'Jimmy intenta mantener el control sobre una vida cada vez más inestable.', 60, '2022-05-02', '2026-03-26 09:12:49'),
(324, 44, 5, 'Black and Blue', 'Howard empieza a sospechar.', 60, '2022-05-09', '2026-03-26 09:12:49'),
(325, 44, 6, 'Axe and Grind', 'El gran plan sigue avanzando.', 60, '2022-05-16', '2026-03-26 09:12:49'),
(326, 44, 7, 'Plan and Execution', 'Todo culmina en una noche desastrosa.', 60, '2022-05-23', '2026-03-26 09:12:49'),
(327, 44, 8, 'Point and Shoot', 'Las consecuencias son inmediatas y brutales.', 60, '2022-07-11', '2026-03-26 09:12:49'),
(328, 44, 9, 'Fun and Games', 'Un ciclo se cierra y empieza otro.', 60, '2022-07-18', '2026-03-26 09:12:49'),
(329, 44, 10, 'Nippy', 'Gene Takavic ocupa ahora el centro de la historia.', 60, '2022-07-25', '2026-03-26 09:12:49'),
(330, 44, 11, 'Breaking Bad', 'Las líneas entre el pasado y el presente se cruzan.', 60, '2022-08-01', '2026-03-26 09:12:49'),
(331, 44, 12, 'Waterworks', 'El peso de las decisiones del pasado alcanza a todos.', 60, '2022-08-08', '2026-03-26 09:12:49'),
(332, 44, 13, 'Saul Gone', 'Jimmy McGill afronta el final de su recorrido.', 72, '2022-08-15', '2026-03-26 09:12:49'),
(333, 45, 1, 'The Long Bright Dark', 'Rust Cohle y Marty Hart empiezan a contar su historia.', 60, '2014-01-12', '2026-03-26 09:12:50'),
(334, 45, 2, 'Seeing Things', 'La investigación se complica con nuevas pistas.', 60, '2014-01-19', '2026-03-26 09:12:50'),
(335, 45, 3, 'The Locked Room', 'El caso revela una dimensión más inquietante.', 60, '2014-01-26', '2026-03-26 09:12:50'),
(336, 45, 4, 'Who Goes There', 'La operación encubierta desemboca en una secuencia clave.', 60, '2014-02-09', '2026-03-26 09:12:50'),
(337, 45, 5, 'The Secret Fate of All Life', 'El tiempo y la obsesión pesan sobre los protagonistas.', 60, '2014-02-16', '2026-03-26 09:12:50'),
(338, 45, 6, 'Haunted Houses', 'El caso vuelve a abrir viejas heridas.', 60, '2014-02-23', '2026-03-26 09:12:50'),
(339, 45, 7, 'After You\'ve Gone', 'Las piezas empiezan a encajar en el tramo final.', 60, '2014-03-02', '2026-03-26 09:12:50'),
(340, 45, 8, 'Form and Void', 'La primera investigación llega a su desenlace.', 60, '2014-03-09', '2026-03-26 09:12:50'),
(363, 49, 1, 'Episodio 1', 'Tommy Shelby pone en marcha sus primeros movimientos mientras el inspector Campbell llega a la ciudad.', 58, '2013-09-12', '2026-03-26 12:02:17'),
(364, 49, 2, 'Episodio 2', 'La presión policial aumenta y la familia Shelby empieza a tomar posiciones.', 58, '2013-09-19', '2026-03-26 12:02:17'),
(365, 49, 3, 'Episodio 3', 'Tommy intenta consolidar su poder mientras surgen conflictos internos.', 58, '2013-09-26', '2026-03-26 12:02:17'),
(366, 49, 4, 'Episodio 4', 'Las tensiones entre bandas y policía empujan la situación al límite.', 58, '2013-10-03', '2026-03-26 12:02:17'),
(367, 49, 5, 'Episodio 5', 'Tommy prepara un nuevo golpe mientras sus enemigos estrechan el cerco.', 58, '2013-10-10', '2026-03-26 12:02:17'),
(368, 49, 6, 'Episodio 6', 'La primera temporada cierra con un gran enfrentamiento por el futuro de la familia.', 58, '2013-10-17', '2026-03-26 12:02:17'),
(369, 50, 1, 'Episodio 1', 'Los Shelby avanzan hacia Londres para ampliar su imperio criminal.', 58, '2014-10-02', '2026-03-26 12:02:17'),
(370, 50, 2, 'Episodio 2', 'Tommy debe maniobrar entre nuevas alianzas y viejos rivales.', 58, '2014-10-09', '2026-03-26 12:02:17'),
(371, 50, 3, 'Episodio 3', 'La expansión del negocio trae nuevas traiciones.', 58, '2014-10-16', '2026-03-26 12:02:17'),
(372, 50, 4, 'Episodio 4', 'Los Shelby se acercan a un conflicto mayor con organizaciones rivales.', 58, '2014-10-23', '2026-03-26 12:02:17'),
(373, 50, 5, 'Episodio 5', 'La familia afronta las consecuencias de sus decisiones más arriesgadas.', 58, '2014-10-30', '2026-03-26 12:02:17'),
(374, 50, 6, 'Episodio 6', 'La temporada termina con Tommy apostándolo todo.', 58, '2014-11-06', '2026-03-26 12:02:17'),
(375, 51, 1, 'Episodio 1', 'Tommy entra en un peligroso escenario político y criminal internacional.', 58, '2016-05-05', '2026-03-26 12:02:17'),
(376, 51, 2, 'Episodio 2', 'Los Shelby quedan atrapados en una operación mucho mayor de lo esperado.', 58, '2016-05-12', '2026-03-26 12:02:17'),
(377, 51, 3, 'Episodio 3', 'La familia intenta mantener el control en medio del caos.', 58, '2016-05-19', '2026-03-26 12:02:17'),
(378, 51, 4, 'Episodio 4', 'Tommy se enfrenta a nuevas amenazas dentro y fuera de casa.', 58, '2016-05-26', '2026-03-26 12:02:17'),
(379, 51, 5, 'Episodio 5', 'Las tensiones explotan cuando todo parece desmoronarse.', 58, '2016-06-02', '2026-03-26 12:02:17'),
(380, 51, 6, 'Episodio 6', 'El final de temporada deja a los Shelby en una situación crítica.', 58, '2016-06-09', '2026-03-26 12:02:17'),
(381, 52, 1, 'Episodio 1', 'Una amenaza del pasado regresa para vengarse de la familia Shelby.', 58, '2017-11-15', '2026-03-26 12:02:17'),
(382, 52, 2, 'Episodio 2', 'Tommy reúne a su familia para resistir el ataque.', 58, '2017-11-22', '2026-03-26 12:02:17'),
(383, 52, 3, 'Episodio 3', 'La guerra con sus enemigos se vuelve cada vez más personal.', 58, '2017-11-29', '2026-03-26 12:02:17'),
(384, 52, 4, 'Episodio 4', 'Los Shelby preparan una respuesta decisiva.', 58, '2017-12-06', '2026-03-26 12:02:17'),
(385, 52, 5, 'Episodio 5', 'Tommy intenta adelantarse a un desenlace fatal.', 58, '2017-12-13', '2026-03-26 12:02:17'),
(386, 52, 6, 'Episodio 6', 'La cuarta temporada culmina con una victoria de alto coste.', 58, '2017-12-20', '2026-03-26 12:02:17'),
(387, 53, 1, 'Episodio 1', 'Tommy entra en política mientras Europa se vuelve más inestable.', 58, '2019-08-25', '2026-03-26 12:02:17'),
(388, 53, 2, 'Episodio 2', 'El poder político trae nuevos enemigos y nuevas obsesiones.', 58, '2019-09-01', '2026-03-26 12:02:17'),
(389, 53, 3, 'Episodio 3', 'Tommy se adentra en un juego de manipulación más peligroso que nunca.', 58, '2019-09-08', '2026-03-26 12:02:17'),
(390, 53, 4, 'Episodio 4', 'La presión mental sobre Tommy crece mientras se complica la trama política.', 58, '2019-09-15', '2026-03-26 12:02:17'),
(391, 53, 5, 'Episodio 5', 'Las alianzas empiezan a resquebrajarse.', 58, '2019-09-22', '2026-03-26 12:02:17'),
(392, 53, 6, 'Episodio 6', 'La temporada cierra con uno de los peores momentos de Tommy Shelby.', 58, '2019-09-22', '2026-03-26 12:02:17'),
(393, 54, 1, 'Episodio 1', 'Tommy intenta recomponerse tras los últimos acontecimientos.', 58, '2022-02-27', '2026-03-26 12:02:17'),
(394, 54, 2, 'Episodio 2', 'Nuevos problemas de salud, poder y familia amenazan a Tommy.', 58, '2022-03-06', '2026-03-26 12:02:17'),
(395, 54, 3, 'Episodio 3', 'Los Shelby vuelven a moverse en un entorno cada vez más hostil.', 58, '2022-03-13', '2026-03-26 12:02:17'),
(396, 54, 4, 'Episodio 4', 'La guerra personal y política alcanza un nuevo nivel.', 58, '2022-03-20', '2026-03-26 12:02:17'),
(397, 54, 5, 'Episodio 5', 'Tommy se acerca a la verdad que puede cambiarlo todo.', 58, '2022-03-27', '2026-03-26 12:02:17'),
(398, 54, 6, 'Episodio 6', 'La historia principal de Peaky Blinders llega a su gran desenlace.', 82, '2022-04-03', '2026-03-26 12:02:17'),
(399, 55, 1, 'El final', 'Lucy sale del refugio y su visión del mundo cambia para siempre.', 60, '2024-04-10', '2026-03-26 12:02:17'),
(400, 55, 2, 'El objetivo', 'Los caminos de Lucy, Maximus y el Necrófago se cruzan en el Yermo.', 60, '2024-04-10', '2026-03-26 12:02:17'),
(401, 55, 3, 'La cabeza', 'Una misión extraña pone a todos en peligro.', 60, '2024-04-10', '2026-03-26 12:02:17'),
(402, 55, 4, 'Los necrófagos', 'El pasado del Necrófago y la dureza del Yermo salen a la luz.', 60, '2024-04-10', '2026-03-26 12:02:17'),
(403, 55, 5, 'El pasado', 'Nuevas revelaciones cambian la percepción del conflicto.', 60, '2024-04-10', '2026-03-26 12:02:17'),
(404, 55, 6, 'La trampa', 'La tensión entre facciones crece con rapidez.', 60, '2024-04-10', '2026-03-26 12:02:17'),
(405, 55, 7, 'La regla de oro', 'Los protagonistas se acercan a la verdad sobre el sistema del refugio.', 60, '2024-04-10', '2026-03-26 12:02:17'),
(406, 55, 8, 'El comienzo', 'La temporada cierra uniendo el pasado y el futuro del Yermo.', 60, '2024-04-10', '2026-03-26 12:02:17'),
(407, 56, 1, 'Episodio 1', 'Arranca una nueva etapa en el Yermo con nuevos peligros y alianzas.', 60, '2025-12-16', '2026-03-26 12:02:17'),
(408, 56, 2, 'Episodio 2', 'Lucy y sus aliados avanzan hacia un territorio más hostil.', 60, '2025-12-23', '2026-03-26 12:02:17'),
(409, 56, 3, 'Episodio 3', 'La búsqueda de respuestas se vuelve más peligrosa.', 60, '2025-12-30', '2026-03-26 12:02:17'),
(410, 56, 4, 'Episodio 4', 'La guerra entre intereses enfrentados se intensifica.', 60, '2026-01-06', '2026-03-26 12:02:17'),
(411, 56, 5, 'Episodio 5', 'Los personajes afrontan pérdidas y revelaciones clave.', 60, '2026-01-13', '2026-03-26 12:02:17'),
(412, 56, 6, 'Episodio 6', 'La tensión aumenta mientras el plan principal avanza.', 60, '2026-01-20', '2026-03-26 12:02:17'),
(413, 56, 7, 'Episodio 7', 'Todo se acerca a un nuevo clímax.', 60, '2026-01-27', '2026-03-26 12:02:17'),
(414, 56, 8, 'Episodio 8', 'La segunda temporada cierra con nuevas puertas abiertas para el futuro.', 60, '2026-02-03', '2026-03-26 12:02:17'),
(415, 57, 1, 'Currahee', 'Los hombres de la Compañía Easy se preparan para la guerra durante su duro entrenamiento.', 70, '2001-09-09', '2026-03-26 12:02:17'),
(416, 57, 2, 'Día de días', 'La invasión de Normandía pone a prueba a la compañía.', 60, '2001-09-16', '2026-03-26 12:02:17'),
(417, 57, 3, 'Carentan', 'La lucha por Carentan deja una profunda huella en los soldados.', 60, '2001-09-23', '2026-03-26 12:02:17'),
(418, 57, 4, 'Reemplazos', 'La compañía recibe nuevos hombres antes de una nueva ofensiva.', 60, '2001-09-30', '2026-03-26 12:02:17'),
(419, 57, 5, 'Cruce de caminos', 'Los combates continúan mientras algunos líderes deben tomar decisiones críticas.', 60, '2001-10-07', '2026-03-26 12:02:17'),
(420, 57, 6, 'Bastogne', 'La guerra alcanza uno de sus puntos más duros en Bastogne.', 60, '2001-10-14', '2026-03-26 12:02:17'),
(421, 57, 7, 'Punto de ruptura', 'La presión física y mental de la guerra pasa factura.', 60, '2001-10-21', '2026-03-26 12:02:17'),
(422, 57, 8, 'La última patrulla', 'La compañía realiza una peligrosa misión antes del final de la guerra.', 60, '2001-10-28', '2026-03-26 12:02:17'),
(423, 57, 9, 'Por qué luchamos', 'Los soldados descubren de primera mano el horror del nazismo.', 60, '2001-11-04', '2026-03-26 12:02:17'),
(424, 57, 10, 'Puntos', 'La guerra termina y cada hombre afronta su regreso de forma distinta.', 90, '2001-11-04', '2026-03-26 12:02:17'),
(425, 58, 1, '3 de la mañana', 'Frank Castle empieza a descubrir que su guerra todavía no ha terminado.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(426, 58, 2, 'Dos hombres muertos', 'La conspiración se hace más grande y más peligrosa.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(427, 58, 3, 'Kandahar', 'El pasado militar de Frank vuelve a perseguirlo.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(428, 58, 4, 'Reabastecimiento', 'Frank intenta recomponer piezas mientras aumentan los enemigos.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(429, 58, 5, 'Pistola de asalto', 'La violencia se intensifica y las alianzas se complican.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(430, 58, 6, 'El verdugo', 'Frank se acerca a una parte crucial de la verdad.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(431, 58, 7, 'Cruce de caminos', 'Las líneas entre aliados y enemigos empiezan a borrarse.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(432, 58, 8, 'Aguas frías y profundas', 'La investigación entra en una fase más oscura.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(433, 58, 9, 'Delante de frente', 'Frank y sus rivales se acercan al choque definitivo.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(434, 58, 10, 'La virtud del bueno', 'Las decisiones personales pesan tanto como la misión.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(435, 58, 11, 'Peligro cercano', 'La guerra estalla de forma abierta.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(436, 58, 12, 'Cuartel general', 'Frank encara el núcleo de la conspiración.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(437, 58, 13, 'Memento Mori', 'La primera temporada cierra con Frank abrazando su destino.', 55, '2017-11-17', '2026-03-26 12:02:17'),
(438, 59, 1, 'Camino al infierno', 'Frank intenta mantenerse al margen, pero pronto vuelve a la acción.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(439, 59, 2, 'Lucha o huida', 'Una nueva amenaza obliga a Frank a implicarse.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(440, 59, 3, 'Problemas de barrio', 'Frank descubre que la situación es más grande de lo que parecía.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(441, 59, 4, 'Cicatriz', 'Las heridas del pasado y del presente siguen abiertas.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(442, 59, 5, 'Una mano amiga', 'Frank recibe ayuda, pero también nuevas complicaciones.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(443, 59, 6, 'Nakazat', 'El conflicto escala de forma brutal.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(444, 59, 7, 'Una última vez', 'Los personajes se acercan a un punto de no retorno.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(445, 59, 8, 'Mi hermano guardián', 'Las relaciones personales se ven empujadas al límite.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(446, 59, 9, 'Explosionando para siempre', 'La guerra se vuelve abierta e inevitable.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(447, 59, 10, 'Oscura entraña', 'Frank y sus enemigos ajustan cuentas.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(448, 59, 11, 'La tormenta', 'Todo empieza a derrumbarse antes del final.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(449, 59, 12, 'Colisión', 'Los caminos de todos los implicados chocan violentamente.', 55, '2019-01-18', '2026-03-26 12:02:17'),
(450, 59, 13, 'El remolino', 'La serie concluye con Frank Castle totalmente convertido en The Punisher.', 55, '2019-01-18', '2026-03-26 12:02:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favorito`
--

CREATE TABLE `favorito` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_pelicula` int(11) NOT NULL,
  `creado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `favorito`
--

INSERT INTO `favorito` (`id`, `id_usuario`, `id_pelicula`, `creado`) VALUES
(6, 17, 18, '2026-03-24 08:21:33'),
(7, 17, 14, '2026-03-24 08:54:35'),
(9, 17, 5, '2026-03-24 11:23:46'),
(10, 17, 24, '2026-03-24 11:48:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

CREATE TABLE `genero` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`id`, `nombre`) VALUES
(9, 'Acción'),
(8, 'Animación'),
(6, 'Anime'),
(3, 'Aventura'),
(1, 'Ciencia Ficción'),
(4, 'Comedia'),
(2, 'Drama'),
(7, 'Romantica'),
(5, 'Terror');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticia`
--

CREATE TABLE `noticia` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `imagen` varchar(255) DEFAULT NULL COMMENT 'Ruta o nombre del archivo de la imagen',
  `publicado` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `noticia`
--

INSERT INTO `noticia` (`id`, `titulo`, `contenido`, `imagen`, `publicado`) VALUES
(1, 'Nominaciones a los Premios Globo de Oro Anunciadas', 'Empiezan las nominaciones para los globos de oro', 'noticia-nominaciones-a-los-premios-globo-de-oro-anunciadas_20260325_133537_715e4d1f.webp', '2025-11-27 23:00:00'),
(2, 'Primer Vistazo a \"Avengers Doomsday\"', 'Se revela una parte del elenco para la próxima entrega de los Vengadores, entre ellos vuelve la estrella Robert Downey J.R (Iron Man)', 'noticia-primer-vistazo-a-avengers-doomsday_20260325_133544_363fdef7.webp', '2025-03-12 23:00:00'),
(3, 'No hay episodio final', 'El supuesto episodio final de Stranger Things se ha emitido sin grandes giros ni acontecimientos relevantes. La historia transcurre con normalidad en Hawkins, sin nuevas amenazas ni cambios.', 'noticia-no-hay-episodio-final_20260325_122943_559b5057.webp', '2026-01-09 10:22:53'),
(4, 'Amazon revela el actor de Kratos en la serie de \'God of War\'', 'Prime Video ha anunciado que Ryan Hurst, conocido por ser Opie Winston en \'Sons of Anarchy\' o Beta en \'The Walking Dead\', será el encargado de interpretar a Kratos en \'God of War\', la adaptación televisiva y en acción real de la famosa serie de videojuegos distribuidos por Sony Computer Entertainment.\r\n\r\nPor su parte Deadline asegura que Teresa Palmer será la encargada de interpretar a Phoebe/Sif, la diosa nórdica de la Tierra y la Cosecha y esposa de un Thor a quien interpretará Ólafur Darri Ólafsson, si bien por el momento Prime Video no lo ha confirmado (o desmentido) de manera oficial el fichaje de ninguno de los dos.\r\n\r\nRonald D. Moore será el creador y showrunner de esta serie en la que lleva trabajando desde octubre de 2024, cuando entró en el proyecto para sustituir a Rafe Judkins (guionista de \'Uncharted\' y creador de \'La rueda del tiempo\') y a los productores ejecutivos Hawk Ostby y Mark Fergus (guionistas de \'Hijos de los hombres\' y creadores de \'The Expanse\').\r\n\r\nUna serie ya en preproducción en Vancouver que, según la sinopsis facilitada por Prime Video, seguirá a un padre y a un hijo, Kratos y Atreus, en su viaje para esparcir las cenizas de su esposa y madre, Faye. A lo largo de sus aventuras, Kratos intentará enseñar a su hijo a ser un mejor Dios... mientras que Atreus intentará enseñar a su padre a ser un mejor ser humano.\r\n\r\nFrederick E.O. Toye (\'The Boys\', \'Fallout\') será el director de los dos primeros episodios de \'God of War\', una producción de Sony Pictures Television, PlayStation Productions, Amazon MGM Studios y Tall Ship Productions que contará con al menos dos temporadas. \r\n\r\nPor último comentar que si bien es conocido sobre todo por su trabajo al frente de \'Star Trek: La nueva generación\', \'Star Trek: Espacio Profundo Nueve\' y \'Galáctica, estrella de combate\', entre los créditos de Moore también destacan otros títulos como \'Carnivàle\', \'Caprica\', \'Helix\' y sobre todo \'Outlander\' y \'Para toda la humanidad\', las exitosas series de Starz y Apple.', 'noticia-amazon-revela-el-actor-de-kratos-en-la-serie-de-god-of-war_20260325_122923_0c720776.webp', '2026-01-16 11:15:00'),
(5, 'Nominados a la 98ª edición de los Premios Oscar', 'Con ustedes, las nominaciones a la 98ª edición de los premios Óscar. Una edición cuya gala tendrá lugar el próximo 15 de marzo en el Dolby Theatre de Hollywood de Los Ángeles, California. Sin más, a continuación, el listado completo con los nominados.', 'noticia-nominados-a-la-98-edicion-de-los-premios-oscar_20260325_122915_d0df1874.webp', '2026-01-25 17:30:30'),
(6, 'Las películas más esperadas de 2026', 'Llega la hora de echar un vistazo a las fechas clave de cara a los estrenos más esperados, principalmente en cines, de un 2026 que apenas comienza en unas horas. Sin más dilación, aquí les dejo un listado con las que bien podrían ser las películas más exitosas del año ordenadas por fecha de estreno.', 'noticia-las-peliculas-mas-esperadas-de-2026_20260325_133508_150371fd.webp', '2025-12-31 22:00:00'),
(7, 'Primer avance de \'Vengadores: Doomsday\'', 'Marvel Studios presenta online el primero de los cuatro (o cinco...) avances de \'Vengadores: Doomsday\' que actuarán como teloneros de \'Avatar: Fuego y ceniza\' en sus primeras semanas en cines, a razón de uno por semana.', 'noticia-primer-avance-de-vengadores-doomsday_20260325_133524_13e7ed7e.webp', '2025-12-23 11:00:00'),
(8, 'Segundo avance de \'Vengadores: Doomsday\'', 'Marvel Studios presenta online el segundo de los cuatro (o cinco...) avances de \'Vengadores: Doomsday\' que actuarán como teloneros de \'Avatar: Fuego y ceniza\' en sus primeras semanas en cines, a razón de uno por semana.', 'noticia-segundo-avance-de-vengadores-doomsday_20260325_133513_0f82b2b9.webp', '2025-12-30 11:00:00'),
(9, 'Tercer avance de \'Vengadores: Doomsday\'', 'Marvel Studios presenta online el tercero de los cuatro (o cinco...) avances de \'Vengadores: Doomsday\' que han ido actuando estas últimas semanas como teloneros de \'Avatar: Fuego y ceniza\', a razón de uno por semana.', 'noticia-tercer-avance-de-vengadores-doomsday_20260325_122952_b79cc587.webp', '2026-01-06 11:00:00'),
(10, 'Cuarto avance de \'Vengadores: Doomsday\'', 'Marvel Studios presenta online el cuarto (y puede o no que el último) de los avances de \'Vengadores: Doomsday\' que han ido actuando estas últimas semanas como teloneros de \'Avatar: Fuego y ceniza\', a razón de uno por semana.', 'noticia-cuarto-avance-de-vengadores-doomsday_20260325_122935_3b32b019.webp', '2026-01-13 11:00:00'),
(11, 'Ocho horas han sido suficientes para que el tráiler de \'Spider-Man 4\' se haga con un récord histórico', 'Spider-Man: Brand New Day, la cuarta entrega en solitario del hombre araña de Tom Holland, llega a los cines el 31 de julio, pero el filme ya ha roto un récord con su tráiler. El primer adelanto de la nueva aventura del superhéroe de Marvel se ha convertido, básicamente, en el más visto de la historia.\r\n\r\nSegún informa Sony Pictures a través de la firma WaveMetrix, el tráiler de Spider-Man 4 ha alcanzado las 718.6 millones de visualizaciones desde su estreno el pasado miércoles. En solo ocho horas, el adelanto del filme superó al de Deadpool y Lobezno (2024), que consiguió 365 millones de visualizaciones hace dos años. Además, en el caso de la tercera entrega del mercenario bocazas, esa cifra incluía 100 millones de visualizaciones en televisión porque el adelanto se emitió durante la Super Bowl. \r\n\r\nSi lo comparamos con los números de la anterior entrega, No Way Home (2021) alcanzó las 355.5 millones de visualizaciones en las primeras 24 horas. La tercera película del trepamuros, que recuperó a Andrew Garfield y Tobey Maguire como sus respectivos Peter Parker, es la película más taquillera de Sony Pictures. No Way Home recaudó 1.910 millones de dólares y fue el título más exitoso de todo 2021.', 'noticia-ocho-horas-han-sido-suficientes-para-que-el-trailer-de-spider-man-4-se-haga-con-un-record-historico_20260325_122828_c1eb4a6c.webp', '2026-03-20 21:08:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pelicula`
--

CREATE TABLE `pelicula` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `sinopsis` text NOT NULL,
  `poster` varchar(255) DEFAULT NULL COMMENT 'Ruta o nombre del archivo del poster',
  `fecha_estreno` date NOT NULL,
  `duracion` smallint(4) DEFAULT NULL COMMENT 'Duración en minutos',
  `edad` varchar(10) DEFAULT 'TP' COMMENT 'Clasificación por edad (ej: +12, +18)',
  `id_genero` int(11) DEFAULT NULL,
  `trailer` varchar(255) DEFAULT NULL COMMENT 'URL o ruta del trailer de la película'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pelicula`
--

INSERT INTO `pelicula` (`id`, `titulo`, `sinopsis`, `poster`, `fecha_estreno`, `duracion`, `edad`, `id_genero`, `trailer`) VALUES
(1, 'Deadpool y Lobezno', 'Deadpool se une a Lobezno para una aventura llena de acción y humor negro.', 'pelicula-deadpool-y-lobezno_20260325_133416_78c45102.webp', '2025-07-26', 125, '+18', 1, 'https://www.youtube.com/embed/tTM5weeCFvQ'),
(2, 'Wicked 2', 'La secuela del musical Wicked, explorando más a fondo la historia de Elphaba y Glinda.', 'pelicula-wicked-2_20260325_133408_614c786d.webp', '2025-12-12', 150, '+7', 3, 'https://www.youtube.com/embed/wweDnEbMvtY'),
(3, 'Chainsaw Man - la película: arco de Reze', 'Denji se enfrenta a nuevos demonios y desafíos en esta adaptación cinematográfica del popular manga/anime, llena de acción y terror.', 'pelicula-chainsaw-man-la-pelicula-arco-de-reze_20260325_133400_91481e55.webp', '2025-10-24', 120, '+16', 6, 'https://www.youtube.com/embed/-KpVet_60Yc'),
(4, 'Torrente Presidente', 'Torrente vuelve con sus disparatadas aventuras y su característico humor irreverente.', 'pelicula-torrente-presidente_20260325_133351_1b937118.webp', '2026-03-06', 105, '+18', 4, 'https://www.youtube.com/embed/mMbhTWubpqs'),
(5, 'Avatar | Fuego y Ceniza', 'La esperada tercera entrega de la saga Avatar, con nuevas aventuras en Pandora.', 'pelicula-avatar-fuego-y-ceniza_20260325_133342_7fd2ff42.webp', '2025-12-20', 180, '+12', 1, 'https://www.youtube.com/embed/bf38f_JINyw'),
(6, 'F1', 'Una película emocionante sobre la temporada más intensa de la Fórmula 1, con rivalidades y velocidad extrema.', 'pelicula-f1_20260325_133334_3909c31b.webp', '2025-11-30', 140, '+7', 3, 'https://www.youtube.com/embed/pA1bcofwBLQ'),
(7, 'FNAF 2', 'Un thriller de terror basado en el popular videojuego Five Nights at Freddy\'s 2. Sustos asegurados.', 'pelicula-fnaf-2_20260325_133326_fa36d99c.webp', '2025-12-08', 95, '+16', 5, 'https://www.youtube.com/embed/d_St1yVHIsI'),
(8, 'Vengadores: Doomsday', 'Los Vengadores se enfrentan a una amenaza apocalíptica que pondrá en riesgo todo el universo. Acción y aventuras épicas aseguradas.', 'pelicula-vengadores-doomsday_20260325_133318_66fa3f64.webp', '2026-12-18', 160, '+12', 1, ''),
(9, 'Spiderman: Brand New Day', 'Spiderman enfrenta nuevos villanos y desafíos en la ciudad de Nueva York mientras equilibra su vida personal y sus responsabilidades heroicas.', 'pelicula-spiderman-brand-new-day_20260325_133259_5ebd41d9.webp', '2026-07-31', 145, '+12', 1, 'https://www.youtube.com/embed/owfWVJoxXR4?si=-mfM18_6_iMgFaeM'),
(10, 'Return to Silent Hill', 'Una nueva entrega del terror psicológico en Silent Hill donde los secretos oscuros del pueblo emergen nuevamente, poniendo a los protagonistas al límite.', 'pelicula-return-to-silent-hill_20260325_133238_6e95565d.webp', '2026-01-23', 100, '+16', 5, 'https://www.youtube.com/embed/L8FDqD_PkBk'),
(11, 'Zootropolis 2', 'Judy y Nick se encuentran tras la retorcida pista de un misterioso reptil que llega a Zootopia y pone patas arriba la metrópolis de los mamíferos. Para resolver el caso, Judy y Nick deben ir de incógnito a nuevas partes inesperadas de la ciudad, donde su creciente asociación se pone a prueba como nunca antes.', 'pelicula-zootropolis-2_20260325_133231_bc0f5c20.webp', '2025-11-28', 115, '+7', 4, 'https:///www.youtube.com/embed/io6VfjLpgaU'),
(12, 'Toy story 5', 'Toy Story 5 es una anunciada cinta animada, que, por estar en etapas iniciales de preproducción, no se sabe sobre su historia, solo que, después de la aparente despedida de los personajes, la cinta volverá a reunir a Woody y a Buzz para una aventura completamente renovada y con nuevos y entrañables personajes', 'pelicula-toy-story-5_20260325_133222_c27bdc1b.webp', '2026-06-19', 125, '+7', 3, 'https://www.youtube.com/embed/OzoF4O_JVGg'),
(13, 'La Odisea', 'El épico y peligroso viaje de Odiseo, rey de Ítaca, para regresar a casa después de la Guerra de Troya, enfrentándose a dioses, monstruos (cíclopes, sirenas) y retos sobrenaturales, mientras su leal esposa Penélope y su hijo Telémaco lidian con pretendientes en su reino.', 'pelicula-la-odisea_20260325_133205_5088c840.webp', '2026-07-17', 176, '+18', 3, 'https://www.youtube.com/embed/NUIIc9gZOcQ'),
(14, 'Super Mario Galaxy: La película', 'Nuestro plomero favorito estará de regreso en una nueva aventura! Tras el rotundo éxito en taquilla con ‘Super Mario Bros. La película´, el director de Illumination Entertainment, Chris Melandri y el propio creador, Shigeru Miyamoto, anunciaron la tan esperada secuela del personaje más emblemático de los videojuegos de Nintendo. Con una trama desconocida hasta la fecha, se reveló que expandirán aún más el universo de Mario Bros con una historia aún más brillante y divertida.', 'pelicula-super-mario-galaxy-la-pelicula_20260325_133156_6b76a282.webp', '2026-04-03', 120, '+6', 4, 'https://www.youtube.com/embed/Hz57G2KaAp8'),
(15, 'Supergirl: La Mujer del Mañana', 'se centra en una Supergirl más ruda y traumatizada, inspirada en el cómic de Tom King, donde Kara Zor-El, criada en los restos de Krypton y testigo de horrores, se embarca en una aventura vengativa junto a una niña kryptoniana llamada Rutie, buscando venganza por el asesinato del padre de Rutie, en un viaje galáctico que explora la diferencia entre su visión del heroísmo y la de Superman, con tonos más salvajes y maduros.', 'pelicula-supergirl-la-mujer-del-manana_20260325_133138_588c6d4b.webp', '2026-06-26', 135, '+12', 1, 'https://www.youtube.com/embed//BqmDRWORkbI'),
(16, 'Aída y Vuelta', 'Tras la muerte de su padre, Aída hereda la vieja casa familiar en el barrio y se muda con sus dos hijos. Pero las dificultades económicas la obligan a trabajar como empleada de limpieza, mientras debe compartir la vivienda con su madre y su hermano.', 'pelicula-aida-y-vuelta_20260325_133127_7491b982.webp', '2026-01-30', 90, '+6', 4, 'https://www.youtube.com/embed/O82ibLC4bmM'),
(17, '28 Años Despues: El templo de los Huesos', 'Ampliando el mundo creado por Danny Boyle y Alex Garland en ‘28 años después’, pero dándole un giro radical, Nia DaCosta dirige ‘28 años después: El templo de los huesos’. En la continuación de esta épica historia, el Dr. Kelson (Ralph Fiennes) se ve envuelto en una nueva y sorprendente relación, cuyas consecuencias podrían cambiar el mundo tal y como lo conocen, y el encuentro de Spike (Alfie Williams) con Jimmy Crystal (Jack O\'Connell) se convierte en una pesadilla de la que no puede escapar. En el mundo de The Bone Temple, los infectados ya no son la mayor amenaza para la supervivencia: la inhumanidad de los supervivientes puede ser aún más extraña y aterradora.', 'pelicula-28-anos-despues-el-templo-de-los-huesos_20260325_133117_09bb4d61.webp', '2026-01-16', 150, '+18', 5, 'https://www.youtube.com/embed/PTQ1naoCw88'),
(18, 'Michael', 'biopic dirigida por Antoine Fuqua que narra la vida del \"Rey del Pop\", desde sus inicios en los Jackson 5 hasta su ascenso a ícono global, explorando su talento, desafíos, su obra maestra como Thriller, su compra de Neverland, y culminando con su trágica muerte en 2009, ofreciendo una mirada íntima a su legado y humanidad, protagonizada por su sobrino Jaafar Jackson.', 'pelicula-michael_20260325_133107_dd994d8d.webp', '2026-04-24', 210, '+12', 2, 'https://www.youtube.com/embed/j-JXqygdRnE'),
(19, 'Dune: Parte tres', 'Dirigida por Denis Villeneuve y se estrenará el 18 de diciembre de 2026. Será la conclusión de la trilogía de Villeneuve, adaptando la novela de Frank Herbert y mostrando las consecuencias del ascenso mesiánico de Paul Atreides, con el regreso de Timothée Chalamet, Zendaya, Rebecca Ferguson, Florence Pugh y la posible incorporación de Robert Pattinson.', 'pelicula-dune-parte-tres_20260325_133057_d468e2eb.webp', '2026-12-18', 175, '+18', 1, 'https://www.youtube.com/embed/Tkb_GGcJ8j8?si=gif4Ieoj_uGfjPG8'),
(20, 'Ruben y los pesaos', 'Rubén y su grupo de amigos, los Pesados, pasan de los líos cotidianos a verse envueltos en un conflicto épico que amenaza su mundo. Entre acción, humor y caos, Rubén tendrá que convertirse en líder y demostrar que la amistad y el valor pueden plantar cara a cualquier guerra.', 'pelicula-ruben-y-los-pesaos_20260325_122745_385ae554.webp', '2026-01-28', 200, '+18', 1, ''),
(21, 'Los amantes Prohibidos 💔', 'Darío, un agente de élite atrapado en una organización peligrosa, ve cómo su mundo se tambalea al conocer a Turpin, alguien que guarda un secreto capaz de destruirlo todo. Entre persecuciones, traiciones y acción constante, ambos inician un amor prohibido que los obliga a elegir entre obedecer al sistema o arriesgarlo todo por estar juntos.', 'pelicula-los-amantes-prohibidos_20260325_122730_4d465a73.webp', '2026-01-28', 180, '+18', 2, ''),
(22, 'Cars', 'El aspirante a campeón de carreras Rayo McQueen parece que está a punto de conseguir el éxito. Su actitud arrogante se desvanece cuando llega a una pequeña comunidad olvidada que le enseña las cosas importantes de la vida que había olvidado.', 'pelicula-cars_20260325_122719_c629494d.webp', '2026-03-23', 110, '+7', 8, 'https://www.youtube.com/embed/1uq5eJHwio4?si=V-VCU4azRW_zmXs4'),
(23, 'Spider-Man: un nuevo universo', 'Tras ser picado por una araña y adquirir superpoderes, el adolescente Miles Morales se dedica a llevar una doble vida en la que compagina su rutina en el instituto con patrullar la ciudad y perseguir a villanos.', 'pelicula-spider-man-un-nuevo-universo_20260325_122709_4231e688.webp', '2026-02-13', 120, '+7', 8, 'https://www.youtube.com/embed/k-8ZFn1Askc?si=5wbDcrqoEAyANyjF'),
(24, 'THE BATMAN', 'En su segundo año luchando contra el crimen, Batman explora la corrupción existente en la ciudad de Gotham y el vínculo de esta con su propia familia. Además, entrará en conflicto con un asesino en serie conocido como \"el Acertijo\".', 'pelicula-the-batman_20260325_122351_eba3e307.webp', '2026-01-28', 180, '+16', 9, 'https://www.youtube.com/embed/IqRRLA6pZvo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plataforma`
--

CREATE TABLE `plataforma` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `creado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `plataforma`
--

INSERT INTO `plataforma` (`id`, `nombre`, `logo`, `color`, `creado`) VALUES
(1, 'Netflix', 'img/plataformas/netflix.png', '#e50914', '2026-03-25 08:13:09'),
(2, 'HBO Max', 'img/plataformas/hbo.png', '#7f5af0', '2026-03-25 08:13:09'),
(3, 'Disney+', 'img/plataformas/disney.png', '#1f80e0', '2026-03-25 08:13:09'),
(4, 'Prime Video', 'img/plataformas/primevideo.png', '#00a8e1', '2026-03-25 08:13:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyeccion`
--

CREATE TABLE `proyeccion` (
  `id` int(11) NOT NULL,
  `id_pelicula` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `sala` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyeccion`
--

INSERT INTO `proyeccion` (`id`, `id_pelicula`, `fecha`, `hora`, `sala`) VALUES
(13, 1, '2026-12-01', '17:00:00', 'Sala 1'),
(14, 1, '2026-12-01', '20:30:00', 'Sala 1'),
(15, 1, '2026-12-02', '18:00:00', 'Sala 2'),
(16, 2, '2026-12-12', '16:30:00', 'Sala 3'),
(17, 2, '2026-12-13', '21:45:00', 'Sala 3'),
(19, 3, '2026-10-24', '17:00:00', 'Sala 1'),
(20, 3, '2026-10-24', '20:00:00', 'Sala 1'),
(21, 3, '2026-10-25', '18:30:00', 'Sala 2'),
(22, 4, '2026-12-01', '16:30:00', 'Sala 3'),
(23, 4, '2026-12-01', '21:00:00', 'Sala 3'),
(24, 4, '2026-12-02', '19:00:00', 'Sala 4'),
(25, 5, '2026-12-20', '18:00:00', 'Sala 5'),
(26, 5, '2026-12-20', '21:30:00', 'Sala 5'),
(27, 5, '2026-12-20', '20:00:00', 'Sala 6'),
(28, 6, '2026-11-30', '17:30:00', 'Sala 2'),
(29, 6, '2026-11-30', '20:15:00', 'Sala 2'),
(30, 6, '2026-12-01', '18:45:00', 'Sala 3'),
(31, 7, '2026-12-08', '19:00:00', 'Sala 4'),
(32, 7, '2026-12-08', '22:00:00', 'Sala 4'),
(33, 7, '2026-12-08', '20:30:00', 'Sala 5'),
(34, 10, '2026-01-29', '19:00:00', 'Sala 2'),
(35, 20, '2026-01-28', '14:00:00', 'Sala 6');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_proyeccion` int(11) NOT NULL,
  `asientos` varchar(100) NOT NULL COMMENT 'Número de asientos o lista de códigos de asientos (ej: 2 o A1,B5)',
  `fecha_reserva` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`id`, `id_usuario`, `id_proyeccion`, `asientos`, `fecha_reserva`) VALUES
(3, 6, 15, '10', '2025-12-01 10:12:14'),
(6, 8, 13, '5', '2026-01-07 07:49:21'),
(7, 8, 13, '17', '2026-01-07 07:53:14'),
(8, 8, 13, '16', '2026-01-07 07:57:38'),
(9, 8, 13, '16', '2026-01-07 07:57:42'),
(10, 8, 13, '17', '2026-01-07 08:45:45'),
(11, 8, 13, '17', '2026-01-07 08:46:59'),
(12, 8, 13, '17', '2026-01-07 08:47:03'),
(13, 8, 13, '16', '2026-01-07 08:48:51'),
(14, 8, 13, '16', '2026-01-07 08:49:21'),
(15, 8, 13, '16', '2026-01-07 08:49:22'),
(16, 8, 13, '16', '2026-01-07 08:49:34'),
(17, 8, 13, 'A1', '2026-01-09 07:37:39'),
(18, 8, 13, 'A3', '2026-01-09 07:39:13'),
(19, 8, 13, 'A3', '2026-01-09 07:43:14'),
(20, 8, 13, 'A2', '2026-01-09 07:56:17'),
(21, 8, 13, 'A3', '2026-01-09 08:05:46'),
(22, 8, 13, 'A1', '2026-01-09 08:06:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sala_config`
--

CREATE TABLE `sala_config` (
  `sala` varchar(50) NOT NULL,
  `filas` int(11) NOT NULL DEFAULT 8,
  `columnas` int(11) NOT NULL DEFAULT 10,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sala_config`
--

INSERT INTO `sala_config` (`sala`, `filas`, `columnas`, `updated_at`) VALUES
('Sala 1', 8, 10, '2026-01-21 08:18:06'),
('Sala 2', 8, 10, '2026-01-21 08:18:06'),
('Sala 3', 8, 10, '2026-01-21 08:18:06'),
('Sala 4', 8, 10, '2026-01-21 08:18:06'),
('Sala 5', 8, 10, '2026-01-21 08:18:06'),
('Sala 6', 8, 10, '2026-01-21 08:18:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serie`
--

CREATE TABLE `serie` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `sinopsis` text NOT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `fecha_estreno` date DEFAULT NULL,
  `edad` varchar(20) DEFAULT NULL,
  `id_genero` int(11) DEFAULT NULL,
  `id_plataforma` int(11) DEFAULT NULL,
  `estado` enum('en_emision','finalizada','proximamente') DEFAULT 'en_emision',
  `destacada` tinyint(1) DEFAULT 0,
  `puntuacion` decimal(3,2) DEFAULT 0.00,
  `creado` timestamp NOT NULL DEFAULT current_timestamp(),
  `trailer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `serie`
--

INSERT INTO `serie` (`id`, `titulo`, `sinopsis`, `poster`, `banner`, `fecha_estreno`, `edad`, `id_genero`, `id_plataforma`, `estado`, `destacada`, `puntuacion`, `creado`, `trailer`) VALUES
(1, 'Stranger Things', 'Un grupo de amigos se enfrenta a sucesos paranormales, experimentos secretos y criaturas aterradoras en un pequeño pueblo.', 'img/series/posters/serie-poster_20260325_132512_ea9929fb.webp', 'img/series/banners/serie-banner_20260325_132513_47b974d9.webp', '2016-07-15', '+16', 1, 1, 'en_emision', 1, 4.80, '2026-03-25 08:16:33', 'https://www.youtube.com/watch?v=b9EkMc79ZSU'),
(2, 'The Last of Us', 'En un mundo devastado, Joel y Ellie emprenden un viaje marcado por la supervivencia, la pérdida y la esperanza.', 'img/series/posters/serie-poster_20260325_132558_838794db.webp', 'img/series/banners/serie-banner_20260325_132558_811ddb65.webp', '2023-01-15', '+18', 1, 2, 'en_emision', 1, 4.90, '2026-03-25 08:16:33', NULL),
(3, 'The Boys', 'Un grupo de vigilantes intenta frenar a unos supuestos superhéroes corruptos y peligrosos.', 'img/series/posters/serie-poster_20260325_132613_67aaef69.webp', 'img/series/banners/serie-banner_20260325_132614_37dd92c1.webp', '2019-07-26', '+18', 1, 4, 'en_emision', 0, 4.60, '2026-03-25 08:16:33', NULL),
(4, 'The Mandalorian', 'Un cazarrecompensas solitario recorre la galaxia en una aventura cargada de acción y misterio.', 'img/series/posters/serie-poster_20260325_132632_4a0bcd5b.webp', 'img/series/banners/serie-banner_20260326_084010_06218907.webp', '2019-11-12', '+12', 1, 3, 'en_emision', 0, 4.50, '2026-03-25 08:16:33', NULL),
(9, 'Los Soprano', 'Un jefe de la mafia de Nueva Jersey intenta equilibrar su vida criminal con los problemas de su familia mientras acude a terapia.', 'img/series/posters/serie-poster_20260326_084914_ef84d781.webp', 'img/series/banners/serie-banner_20260326_093017_69b3bc3c.webp', '1999-01-10', '+18', 2, 2, 'finalizada', 0, 0.00, '2026-03-26 07:49:14', NULL),
(10, 'The Bear', 'Un chef joven y brillante regresa a Chicago para hacerse cargo del pequeño restaurante de su familia.', 'img/series/posters/serie-poster_20260326_085526_5175fa25.webp', 'img/series/banners/serie-banner_20260326_085527_59eb6437.webp', '2022-06-23', '+16', 2, 3, 'en_emision', 0, 0.00, '2026-03-26 07:55:27', NULL),
(12, 'Juego de Tronos', 'Varias familias nobles luchan por el control del Trono de Hierro en un mundo marcado por guerras, traiciones y dragones.', 'img/series/posters/serie-poster_20260326_091000_baeeef11.webp', 'img/series/banners/serie-banner_20260326_091000_9c60fb49.webp', '2011-04-17', '+18', 2, 2, 'finalizada', 1, 4.80, '2026-03-26 08:09:17', NULL),
(16, 'Breaking Bad', 'Un profesor de química con problemas económicos entra en el mundo de la metanfetamina tras recibir un diagnóstico de cáncer.', 'img/series/posters/serie-poster_20260326_101514_f29bdcba.webp', 'img/series/banners/serie-banner_20260326_101612_ae83d74b.webp', '2008-01-20', '+18', 2, 1, 'finalizada', 1, 5.00, '2026-03-26 09:12:49', NULL),
(17, 'Better Call Saul', 'Antes de convertirse en Saul Goodman, Jimmy McGill lucha por abrirse camino como abogado en Albuquerque.', 'img/series/posters/serie-poster_20260326_102238_378e8df3.webp', 'img/series/banners/serie-banner_20260326_102004_9b00f964.webp', '2015-02-08', '+18', 1, 1, 'finalizada', 1, 4.90, '2026-03-26 09:12:49', NULL),
(18, 'True Detective', 'Serie antológica criminal en la que cada temporada presenta un nuevo caso, un nuevo escenario y nuevos protagonistas.', 'img/series/posters/serie-poster_20260326_102403_70aa35fd.webp', 'img/series/banners/serie-banner_20260326_102432_c0357315.webp', '2014-01-12', '+18', 2, 2, 'en_emision', 1, 4.70, '2026-03-26 09:12:49', NULL),
(19, 'Peaky Blinders', 'Thomas Shelby lidera a una peligrosa familia de Birmingham que quiere ascender en el mundo del crimen tras la Primera Guerra Mundial.', 'img/series/posters/serie-poster_20260326_130354_bac2e540.webp', 'img/series/banners/serie-banner_20260326_130354_15cccd0d.webp', '2013-09-12', '+18', 1, 1, 'finalizada', 1, 4.90, '2026-03-26 12:02:16', NULL),
(20, 'Fallout', 'En un mundo arrasado por la guerra nuclear, una joven del Refugio sale al exterior y descubre el verdadero rostro del Yermo.', 'img/series/posters/serie-poster_20260326_131204_294accd0.webp', 'img/series/banners/serie-banner_20260326_131204_7dca9d22.webp', '2024-04-10', '+18', 1, 4, 'en_emision', 1, 4.70, '2026-03-26 12:02:17', NULL),
(21, 'Hermanos de sangre', 'La Compañía Easy recorre Europa durante la Segunda Guerra Mundial en una historia de sacrificio, compañerismo y supervivencia.', 'img/series/posters/serie-poster_20260326_131048_4ff0c8e8.webp', 'img/series/banners/serie-banner_20260326_131048_dc6ca9e0.webp', '2001-09-09', '+16', 1, 2, 'finalizada', 1, 4.90, '2026-03-26 12:02:17', NULL),
(22, 'The Punisher', 'Frank Castle busca venganza y descubre una conspiración que va mucho más allá del asesinato de su familia.', 'img/series/posters/serie-poster_20260326_130739_607f0a0b.webp', 'img/series/banners/serie-banner_20260326_130739_92fa4833.webp', '2017-11-17', '+18', 1, 1, 'finalizada', 1, 4.60, '2026-03-26 12:02:17', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temporada`
--

CREATE TABLE `temporada` (
  `id` int(11) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `numero_temporada` int(11) NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `fecha_estreno` date DEFAULT NULL,
  `creado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `temporada`
--

INSERT INTO `temporada` (`id`, `id_serie`, `numero_temporada`, `titulo`, `descripcion`, `poster`, `fecha_estreno`, `creado`) VALUES
(1, 1, 1, '', 'La desaparición de Will desata una serie de sucesos extraños.', 'img/series/temporadas/temporada_poster_20260325_105751_5baee7e3.jpg', '2016-07-15', '2026-03-25 08:16:58'),
(2, 1, 2, '', 'Nuevas amenazas llegan desde el Otro Lado.', 'img/series/temporadas/temporada-poster_20260325_132938_9160f2cb.webp', '2017-10-27', '2026-03-25 08:16:58'),
(3, 2, 1, '', 'Joel y Ellie comienzan su peligroso viaje.', 'img/series/temporadas/tlou1.jpg', '2023-01-15', '2026-03-25 08:16:58'),
(4, 3, 1, '', 'Los superhéroes no son lo que parecen.', 'img/series/temporadas/boys1.jpg', '2019-07-26', '2026-03-25 08:16:58'),
(5, 9, 1, '', 'Tony Soprano comienza a mostrar signos de ansiedad mientras intenta mantener el control de su familia y del negocio.', 'img/series/temporadas/temporada-poster_20260326_085023_112baadf.webp', '1999-01-10', '2026-03-26 07:50:23'),
(6, 9, 2, '', 'Nuevos conflictos internos y rivalidades ponen a prueba el liderazgo de Tony.', 'img/series/temporadas/temporada-poster_20260326_085219_434404a0.webp', '2000-01-16', '2026-03-26 07:52:19'),
(7, 10, 1, '', 'Carmy intenta levantar el negocio familiar mientras choca con la presión y el caos de la cocina.', 'img/series/temporadas/temporada-poster_20260326_085932_f05aa11b.webp', '2022-06-23', '2026-03-26 07:56:36'),
(8, 10, 2, '', 'El equipo busca reinventar el restaurante y llevarlo a un nuevo nivel.', 'img/series/temporadas/temporada-poster_20260326_085937_2fdda88e.webp', '2023-06-22', '2026-03-26 07:57:48'),
(11, 12, 1, '', 'La familia Stark se ve arrastrada al juego político de Poniente.', 'img/series/temporadas/temporada-poster_20260326_091012_13267f3c.webp', '2011-04-17', '2026-03-26 08:09:17'),
(12, 12, 2, '', 'La guerra por el Trono de Hierro se intensifica mientras nuevas amenazas crecen.', 'img/series/temporadas/temporada-poster_20260326_091022_842b6262.webp', '2012-04-01', '2026-03-26 08:09:17'),
(13, 12, 3, '', 'Las alianzas cambian y la guerra deja algunas de sus consecuencias más crueles.', 'img/series/temporadas/temporada-poster_20260326_091048_6a68b7d6.webp', '2013-03-31', '2026-03-26 08:09:17'),
(14, 12, 4, '', 'La caída de grandes figuras altera el equilibrio del poder en Poniente.', 'img/series/temporadas/temporada-poster_20260326_091117_8f7b6496.webp', '2014-04-06', '2026-03-26 08:09:17'),
(15, 12, 5, '', 'Nuevos líderes surgen mientras crece la amenaza más allá del Muro.', 'img/series/temporadas/temporada-poster_20260326_091134_27c85e2e.webp', '2015-04-12', '2026-03-26 08:09:17'),
(16, 12, 6, '', 'Viejas deudas se cobran y el futuro de los reinos empieza a definirse.', 'img/series/temporadas/temporada-poster_20260326_091240_19803d7b.webp', '2016-04-24', '2026-03-26 08:09:17'),
(17, 12, 7, '', 'La guerra final se aproxima y los grandes bandos se preparan para el choque definitivo.', 'img/series/temporadas/temporada-poster_20260326_091258_3b3b868b.webp', '2017-07-16', '2026-03-26 08:09:17'),
(18, 12, 8, '', 'La batalla por el destino de Poniente llega a su desenlace.', 'img/series/temporadas/temporada-poster_20260326_091331_7765783f.webp', '2019-04-14', '2026-03-26 08:09:17'),
(34, 16, 1, 'Temporada 1', 'Walter White da sus primeros pasos en el mundo criminal junto a Jesse Pinkman.', 'img/series/temporadas/temporada-poster_20260326_101741_f38324e3.webp', '2008-01-20', '2026-03-26 09:12:49'),
(35, 16, 2, 'Temporada 2', 'El negocio crece y las consecuencias personales se vuelven más peligrosas.', 'img/series/temporadas/temporada-poster_20260326_101748_7541c360.webp', '2009-03-08', '2026-03-26 09:12:49'),
(36, 16, 3, 'Temporada 3', 'Walter se hunde cada vez más en una espiral de poder, violencia y decisiones irreversibles.', 'img/series/temporadas/temporada-poster_20260326_101755_42b0a255.webp', '2010-03-21', '2026-03-26 09:12:49'),
(37, 16, 4, 'Temporada 4', 'La guerra con Gus Fring eleva la tensión al máximo.', 'img/series/temporadas/temporada-poster_20260326_101800_adcc9224.webp', '2011-07-17', '2026-03-26 09:12:49'),
(38, 16, 5, 'Temporada 5', 'Walter alcanza su punto más alto y también el principio de su caída definitiva.', 'img/series/temporadas/temporada-poster_20260326_101826_b4f7417a.webp', '2012-07-15', '2026-03-26 09:12:49'),
(39, 17, 1, '', 'Jimmy intenta abrirse camino en el mundo legal mientras lidia con su pasado.', 'img/series/temporadas/temporada-poster_20260326_102047_291ff9b9.webp', '2015-02-08', '2026-03-26 09:12:49'),
(40, 17, 2, '', 'Jimmy se acerca cada vez más al abogado que será Saul Goodman.', 'img/series/temporadas/temporada-poster_20260326_102053_6be2a753.webp', '2016-02-15', '2026-03-26 09:12:49'),
(41, 17, 3, '', 'Las tensiones con Chuck y el ascenso criminal de Gus y Mike cambian las reglas del juego.', 'img/series/temporadas/temporada-poster_20260326_102128_b4a82916.webp', '2017-04-10', '2026-03-26 09:12:49'),
(42, 17, 4, '', 'Jimmy intenta rehacer su vida tras una pérdida decisiva.', 'img/series/temporadas/temporada-poster_20260326_102152_2e91afc7.webp', '2018-08-06', '2026-03-26 09:12:49'),
(43, 17, 5, '', 'Saul Goodman toma forma definitiva mientras el peligro aumenta alrededor de Kim y Jimmy.', 'img/series/temporadas/temporada-poster_20260326_102223_801defaf.webp', '2020-02-23', '2026-03-26 09:12:49'),
(44, 17, 6, '', 'La historia se acerca al universo de Breaking Bad y al destino final de Jimmy.', 'img/series/temporadas/temporada-poster_20260326_102229_17564311.webp', '2022-04-18', '2026-03-26 09:12:49'),
(45, 18, 1, 'Temporada 1', 'Dos detectives de Luisiana reabren un caso de asesinato ritual años después.', 'img/series/temporadas/temporada-poster_20260326_102413_fb091278.webp', '2014-01-12', '2026-03-26 09:12:49'),
(49, 19, 1, 'Temporada 1', 'Tommy Shelby intenta expandir el negocio de su familia mientras un inspector llega a Birmingham.', 'img/series/temporadas/temporada-poster_20260326_130447_77369173.webp', '2013-09-12', '2026-03-26 12:02:16'),
(50, 19, 2, 'Temporada 2', 'Los Shelby amplían su poder hacia Londres y se enfrentan a nuevos enemigos.', 'img/series/temporadas/temporada-poster_20260326_130454_b492cd38.webp', '2014-10-02', '2026-03-26 12:02:16'),
(51, 19, 3, 'Temporada 3', 'La familia entra en un escenario internacional lleno de política, traiciones y peligro.', 'img/series/temporadas/temporada-poster_20260326_130501_e1cbf0a3.webp', '2016-05-05', '2026-03-26 12:02:16'),
(52, 19, 4, 'Temporada 4', 'Una vieja venganza pone contra las cuerdas a los Shelby.', 'img/series/temporadas/temporada-poster_20260326_130550_d33035c0.webp', '2017-11-15', '2026-03-26 12:02:16'),
(53, 19, 5, 'Temporada 5', 'Tommy se mete de lleno en la política mientras crece la amenaza del fascismo.', 'img/series/temporadas/temporada-poster_20260326_130556_8201ad66.webp', '2019-08-25', '2026-03-26 12:02:16'),
(54, 19, 6, 'Temporada 6', 'Tommy encara sus demonios finales en el cierre de la historia principal.', 'img/series/temporadas/temporada-poster_20260326_130603_9696fd62.webp', '2022-02-27', '2026-03-26 12:02:16'),
(55, 20, 1, '', 'Lucy abandona el refugio y descubre un mundo postapocalíptico brutal y absurdo.', 'img/series/temporadas/temporada-poster_20260326_131303_14c0c624.webp', '2024-04-10', '2026-03-26 12:02:17'),
(56, 20, 2, '', 'La historia continúa con Lucy, Maximus y el Necrófago adentrándose en nuevos territorios.', 'img/series/temporadas/temporada-poster_20260326_131311_9d394b08.webp', '2025-12-16', '2026-03-26 12:02:17'),
(57, 21, 1, '', 'La Compañía Easy vive el infierno de la guerra desde el entrenamiento hasta el final del conflicto.', 'img/series/temporadas/temporada-poster_20260326_131059_de0dc989.webp', '2001-09-09', '2026-03-26 12:02:17'),
(58, 22, 1, '', 'Frank Castle descubre una conspiración mientras intenta vengar a su familia.', 'img/series/temporadas/temporada-poster_20260326_130841_e6ae9bb5.webp', '2017-11-17', '2026-03-26 12:02:17'),
(59, 22, 2, '', 'Frank se ve arrastrado a un nuevo conflicto que lo obliga a volver a la guerra.', 'img/series/temporadas/temporada-poster_20260326_130849_3ec820b9.webp', '2019-01-18', '2026-03-26 12:02:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_proyeccion` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `codigo` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ticket`
--

INSERT INTO `ticket` (`id`, `id_usuario`, `id_proyeccion`, `cantidad`, `precio_unitario`, `total`, `codigo`, `created_at`) VALUES
(1, 8, 13, 2, 7.50, 15.00, 'A1D5E232D11B', '2026-01-09 09:13:50'),
(2, 8, 13, 3, 7.50, 22.50, '78845E11CF1F', '2026-01-09 09:21:21'),
(3, 8, 13, 2, 7.50, 15.00, 'C3A32A33D2A3', '2026-01-09 09:22:31'),
(4, 8, 13, 1, 7.50, 7.50, '7043405F362C', '2026-01-09 09:33:15'),
(5, 8, 33, 4, 7.50, 30.00, '66AB8E35BE43', '2026-01-09 09:40:53'),
(7, 8, 33, 1, 7.50, 7.50, '4129DE947843', '2026-01-09 09:41:30'),
(8, 8, 27, 1, 7.50, 7.50, 'C71E3C45E2F5', '2026-01-09 09:50:56'),
(9, 8, 23, 10, 7.50, 75.00, 'DE2D38A0D797', '2026-01-09 09:53:48'),
(10, 8, 16, 1, 7.50, 7.50, '5C0FE4B47003', '2026-01-09 11:52:54'),
(11, 8, 30, 1, 7.50, 7.50, '60581B66C4DB', '2026-01-12 11:32:06'),
(12, 8, 30, 1, 7.50, 7.50, 'C2349B7A5109', '2026-01-12 11:38:27'),
(13, 8, 16, 5, 7.50, 37.50, '63F7664FE87D', '2026-01-15 11:01:08'),
(14, 8, 26, 1, 7.50, 7.50, 'DFB0D928229E', '2026-01-21 08:18:20'),
(15, 8, 27, 1, 7.50, 7.50, '4066FF2EFFB0', '2026-01-21 09:20:41'),
(16, 8, 27, 1, 7.50, 7.50, 'E03A7E36D287', '2026-01-21 10:10:51'),
(17, 9, 27, 6, 7.50, 45.00, 'CAE5CB0796DD', '2026-01-28 09:09:31'),
(18, 9, 24, 1, 7.50, 7.50, '88EB650621FB', '2026-01-28 09:39:02'),
(19, 9, 35, 1, 7.50, 7.50, 'AC5A7569872D', '2026-01-28 12:09:38'),
(20, 9, 27, 1, 7.50, 7.50, 'DAFE7F0A371E', '2026-02-02 09:58:16'),
(21, 11, 27, 3, 7.50, 22.50, '3C8953088D53', '2026-03-09 09:35:10'),
(22, 1, 22, 1, 7.50, 7.50, '2EC0FF15A500', '2026-03-23 11:53:43'),
(24, 8, 22, 1, 7.50, 7.50, '4585A377FF15', '2026-03-23 13:08:22'),
(25, 8, 23, 1, 7.50, 7.50, 'C0E20D125F28', '2026-03-23 13:11:07'),
(26, 17, 23, 1, 7.50, 7.50, '407709854C9B', '2026-03-25 13:02:29'),
(27, 17, 14, 1, 7.50, 7.50, '28A98FB3063B', '2026-03-25 13:08:08'),
(28, 17, 23, 1, 7.50, 7.50, 'E2DBE1097ED4', '2026-03-26 09:51:01'),
(29, 17, 23, 1, 7.50, 7.50, 'DA90014C118F', '2026-03-26 10:10:26'),
(30, 17, 23, 1, 7.50, 7.50, '581BFD988B05', '2026-03-26 10:36:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket_asiento`
--

CREATE TABLE `ticket_asiento` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_proyeccion` int(11) NOT NULL,
  `asiento` varchar(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ticket_asiento`
--

INSERT INTO `ticket_asiento` (`id`, `id_ticket`, `id_proyeccion`, `asiento`, `created_at`) VALUES
(1, 2, 13, 'A2', '2026-01-09 09:21:21'),
(2, 2, 13, 'A3', '2026-01-09 09:21:21'),
(3, 2, 13, 'A4', '2026-01-09 09:21:21'),
(4, 3, 13, 'B2', '2026-01-09 09:22:31'),
(5, 3, 13, 'B3', '2026-01-09 09:22:31'),
(6, 4, 13, 'A7', '2026-01-09 09:33:15'),
(7, 5, 33, 'D4', '2026-01-09 09:40:53'),
(8, 5, 33, 'D5', '2026-01-09 09:40:53'),
(9, 5, 33, 'D6', '2026-01-09 09:40:53'),
(10, 5, 33, 'D7', '2026-01-09 09:40:53'),
(12, 7, 33, 'D2', '2026-01-09 09:41:30'),
(13, 8, 27, 'D4', '2026-01-09 09:50:56'),
(14, 9, 23, 'D5', '2026-01-09 09:53:48'),
(15, 9, 23, 'E5', '2026-01-09 09:53:48'),
(16, 9, 23, 'E4', '2026-01-09 09:53:48'),
(17, 9, 23, 'E6', '2026-01-09 09:53:48'),
(18, 9, 23, 'F5', '2026-01-09 09:53:48'),
(19, 9, 23, 'C7', '2026-01-09 09:53:48'),
(20, 9, 23, 'G3', '2026-01-09 09:53:48'),
(21, 9, 23, 'G6', '2026-01-09 09:53:48'),
(22, 9, 23, 'C4', '2026-01-09 09:53:48'),
(23, 9, 23, 'A2', '2026-01-09 09:53:48'),
(24, 10, 16, 'C2', '2026-01-09 11:52:54'),
(25, 11, 30, 'B5', '2026-01-12 11:32:06'),
(26, 12, 30, 'D5', '2026-01-12 11:38:27'),
(27, 13, 16, 'A5', '2026-01-15 11:01:08'),
(28, 13, 16, 'B4', '2026-01-15 11:01:08'),
(29, 13, 16, 'B6', '2026-01-15 11:01:08'),
(30, 13, 16, 'C5', '2026-01-15 11:01:08'),
(31, 13, 16, 'B5', '2026-01-15 11:01:08'),
(32, 14, 26, 'B2', '2026-01-21 08:18:20'),
(33, 15, 27, 'B3', '2026-01-21 09:20:41'),
(34, 16, 27, 'A4', '2026-01-21 10:10:51'),
(35, 17, 27, 'C10', '2026-01-28 09:09:31'),
(36, 17, 27, 'C9', '2026-01-28 09:09:31'),
(37, 17, 27, 'C8', '2026-01-28 09:09:31'),
(38, 17, 27, 'C7', '2026-01-28 09:09:31'),
(39, 17, 27, 'C6', '2026-01-28 09:09:31'),
(40, 17, 27, 'C5', '2026-01-28 09:09:31'),
(41, 18, 24, 'B3', '2026-01-28 09:39:02'),
(42, 19, 35, 'E7', '2026-01-28 12:09:38'),
(43, 20, 27, 'E3', '2026-02-02 09:58:16'),
(44, 21, 27, 'D5', '2026-03-09 09:35:10'),
(45, 21, 27, 'D6', '2026-03-09 09:35:10'),
(46, 21, 27, 'G5', '2026-03-09 09:35:10'),
(47, 22, 22, 'A3', '2026-03-23 11:53:43'),
(49, 24, 22, 'A5', '2026-03-23 13:08:22'),
(50, 25, 23, 'C9', '2026-03-23 13:11:07'),
(51, 26, 23, 'B4', '2026-03-25 13:02:29'),
(52, 27, 14, 'A4', '2026-03-25 13:08:08'),
(53, 28, 23, 'D6', '2026-03-26 09:51:01'),
(54, 29, 23, 'C1', '2026-03-26 10:10:26'),
(55, 30, 23, 'C2', '2026-03-26 10:36:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `creado` timestamp NULL DEFAULT current_timestamp(),
  `verificado` tinyint(1) NOT NULL DEFAULT 0,
  `token_verificacion` varchar(64) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `username`, `email`, `password_hash`, `rol`, `creado`, `verificado`, `token_verificacion`, `token_expira`, `reset_token`, `reset_expira`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$ruwJc684UCt3oE.EKSNPBu3A.u.FmQLv0fOdScu/bwmEcB1NdMqiG', 'admin', '2026-03-23 09:32:25', 0, NULL, NULL, NULL, NULL),
(2, 'critico', 'critico@mmc.com', '$2y$10$iGgI0zQ6.t5pW3uN0D1L3uA2uXb9n9h0O0X7G9w1E4.Q2t5pW3uN', 'usuario', '2025-12-01 09:06:49', 0, NULL, NULL, NULL, NULL),
(3, 'user1', 'user1@mmc.com', '$2y$10$pLwS7gY2wB8cK9xZ0t5jK2M8rD4n7o6i1D5S7e8f9g4h2j1k0l9m', 'usuario', '2025-12-01 09:31:29', 0, NULL, NULL, NULL, NULL),
(6, 'rubentopesao', 'ruben@gmail.com', '$2y$10$ZpuwP.0cEBh93VOSwGzdwexdo0c.XuHCYSIAgMMBtrV2gZf/OTQMW', 'usuario', '2025-12-01 09:46:56', 0, NULL, NULL, NULL, NULL),
(8, 'turpinicius', 'turpin@gmail.com', '$2y$10$gt5AmqgXNfwxr1XCDxdLruRFn/tyWV6fONm4Co9ZoQl1ifiZkw5Ku', 'usuario', '2025-12-01 12:29:05', 0, NULL, NULL, NULL, NULL),
(9, 'a', 'a@gmail.com', '$2y$10$J08rFl6smVZVTSSyrrw/5uvOMG2xuz9TMTD1/Is9g1MWhgARqWc6.', 'usuario', '2026-01-28 08:08:40', 0, NULL, NULL, NULL, NULL),
(11, 'alexxx', 'elmasguapo@gmail.com', '$2y$10$8YkO78IIGZwAHdeEP9hddO6dpAdxAfx23msUiXX2ic.mFkYhD.b9e', 'usuario', '2026-03-09 08:33:54', 0, NULL, NULL, NULL, NULL),
(16, 'alvaro', 'alvaromurada1@gmail.com', '$2y$10$SEZnDTTcud3Y1q5htZ3vJ.3P4WE6ub6NI3haH9cGpambi4BcWdo3O', 'usuario', '2026-03-23 10:29:37', 0, NULL, NULL, NULL, NULL),
(17, 'b', 'david.monzonlopez@gmail.com', '$2y$10$QG47kKX6vA96KuVa2jop9e5tua54zvPmjURQWlY8bEdwMm3WtW4CC', 'admin', '2026-03-24 07:28:38', 1, NULL, NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `critica`
--
ALTER TABLE `critica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_pelicula` (`id_pelicula`);

--
-- Indices de la tabla `critica_serie`
--
ALTER TABLE `critica_serie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_critica_serie_usuario` (`id_usuario`,`id_serie`),
  ADD KEY `fk_critica_serie_serie` (`id_serie`);

--
-- Indices de la tabla `episodio`
--
ALTER TABLE `episodio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_episodio_temporada` (`id_temporada`);

--
-- Indices de la tabla `favorito`
--
ALTER TABLE `favorito`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_favorito` (`id_usuario`,`id_pelicula`),
  ADD KEY `fk_favorito_pelicula` (`id_pelicula`);

--
-- Indices de la tabla `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `noticia`
--
ALTER TABLE `noticia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pelicula`
--
ALTER TABLE `pelicula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_genero` (`id_genero`);

--
-- Indices de la tabla `plataforma`
--
ALTER TABLE `plataforma`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proyeccion`
--
ALTER TABLE `proyeccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pelicula` (`id_pelicula`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_proyeccion` (`id_proyeccion`);

--
-- Indices de la tabla `sala_config`
--
ALTER TABLE `sala_config`
  ADD PRIMARY KEY (`sala`);

--
-- Indices de la tabla `serie`
--
ALTER TABLE `serie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_serie_genero` (`id_genero`),
  ADD KEY `fk_serie_plataforma` (`id_plataforma`);

--
-- Indices de la tabla `temporada`
--
ALTER TABLE `temporada`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_temporada_serie` (`id_serie`);

--
-- Indices de la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_ticket_usuario` (`id_usuario`),
  ADD KEY `idx_ticket_proyeccion` (`id_proyeccion`);

--
-- Indices de la tabla `ticket_asiento`
--
ALTER TABLE `ticket_asiento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_proyeccion_asiento` (`id_proyeccion`,`asiento`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_proyeccion` (`id_proyeccion`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uq_token_verificacion` (`token_verificacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `critica`
--
ALTER TABLE `critica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `critica_serie`
--
ALTER TABLE `critica_serie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `episodio`
--
ALTER TABLE `episodio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=451;

--
-- AUTO_INCREMENT de la tabla `favorito`
--
ALTER TABLE `favorito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `noticia`
--
ALTER TABLE `noticia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `pelicula`
--
ALTER TABLE `pelicula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `plataforma`
--
ALTER TABLE `plataforma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `proyeccion`
--
ALTER TABLE `proyeccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `serie`
--
ALTER TABLE `serie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `temporada`
--
ALTER TABLE `temporada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `ticket_asiento`
--
ALTER TABLE `ticket_asiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `critica`
--
ALTER TABLE `critica`
  ADD CONSTRAINT `critica_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `critica_ibfk_2` FOREIGN KEY (`id_pelicula`) REFERENCES `pelicula` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `critica_serie`
--
ALTER TABLE `critica_serie`
  ADD CONSTRAINT `fk_critica_serie_serie` FOREIGN KEY (`id_serie`) REFERENCES `serie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_critica_serie_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `episodio`
--
ALTER TABLE `episodio`
  ADD CONSTRAINT `fk_episodio_temporada` FOREIGN KEY (`id_temporada`) REFERENCES `temporada` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `favorito`
--
ALTER TABLE `favorito`
  ADD CONSTRAINT `fk_favorito_pelicula` FOREIGN KEY (`id_pelicula`) REFERENCES `pelicula` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorito_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pelicula`
--
ALTER TABLE `pelicula`
  ADD CONSTRAINT `pelicula_ibfk_1` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id`);

--
-- Filtros para la tabla `proyeccion`
--
ALTER TABLE `proyeccion`
  ADD CONSTRAINT `proyeccion_ibfk_1` FOREIGN KEY (`id_pelicula`) REFERENCES `pelicula` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`id_proyeccion`) REFERENCES `proyeccion` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `serie`
--
ALTER TABLE `serie`
  ADD CONSTRAINT `fk_serie_genero` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_serie_plataforma` FOREIGN KEY (`id_plataforma`) REFERENCES `plataforma` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `temporada`
--
ALTER TABLE `temporada`
  ADD CONSTRAINT `fk_temporada_serie` FOREIGN KEY (`id_serie`) REFERENCES `serie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_ticket_proyeccion` FOREIGN KEY (`id_proyeccion`) REFERENCES `proyeccion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ticket_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ticket_asiento`
--
ALTER TABLE `ticket_asiento`
  ADD CONSTRAINT `fk_ta_proyeccion` FOREIGN KEY (`id_proyeccion`) REFERENCES `proyeccion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ta_ticket` FOREIGN KEY (`id_ticket`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
