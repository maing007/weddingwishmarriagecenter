-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 30, 2026 at 03:53 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `usmanv`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_attendance`
--

CREATE TABLE `admin_attendance` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_attendance`
--

INSERT INTO `admin_attendance` (`id`, `admin_id`, `created_at`) VALUES
(1, 1, '2026-03-25');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `team_leader` varchar(100) DEFAULT NULL,
  `attendance` int(11) DEFAULT 0,
  `total_members` int(11) DEFAULT 0,
  `matches_opened` int(11) DEFAULT 0,
  `matches_declined` int(11) DEFAULT 0,
  `matches_accepted` int(11) DEFAULT 0,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `password_hash`, `created_at`, `status`, `department`, `team_leader`, `attendance`, `total_members`, `matches_opened`, `matches_declined`, `matches_accepted`, `role`) VALUES
(1, 'Muhammad Usman', 'us533gi@gmail.com', '$2y$10$92UVXzMsnkDnwvRtACilxuccPh7A4ABSc3DDOucLe5Yq2C1GSfhOK', '2025-12-16 13:42:52', 'approved', 'admin', NULL, 1, 0, 0, 0, 0, 'super-admin');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `content`, `image`, `status`, `created_at`) VALUES
(1, '✅ Changes made:', NULL, 'Wrapped everything in a Bootstrap container (mt-4 for spacing).\r\n\r\nUsed Bootstrap form classes:\r\n\r\nform-control for inputs and textarea.\r\n\r\nform-select for the dropdown.\r\n\r\nform-label for labels.\r\n\r\nAdded spacing with mb-3.\r\n\r\nButtons styled:\r\n\r\nbtn-success for Publish.\r\n\r\nbtn-secondary for Cancel with ms-2 (margin start).\r\n\r\nrows=\"6\" for textarea for a nicer height.', '1766500144_A2.jpg', 'published', '2025-12-23 14:29:04');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `country_code`, `phone`, `subject`, `description`, `ip_address`, `created_at`) VALUES
(1, 'Muhammad Usman', 'us533gi@gmail.com', '+92', '03496186700', 'anyway', 'testing 123456', '::1', '2025-12-14 08:57:11'),
(2, 'gymkdrljvz', 'eywpkthg@testform.xyz', '+1-242', '+1-966-603-5062', 'qorjjpivxj', 'uzujmrnvorsqgfxhhowlklejdujfth', '2a0c:5700:3133:650:863a:56ff:feac:2de8', '2025-12-28 01:41:22'),
(3, 'Jessefrosy', 'millzvicky@gmail.com', '+92', '88869975192', 'IMPORTANT MESSAGE! CLAIM YOUR $117,901.70 REWARD NOW!', 'URGENT MESSAGE! COLLECT YOUR $117,901.93 CASH: ACT QUICKLY https://www.youtube.com/redirect?q=https://telegra.ph/Youve-earned-11790176-New-transfer--356853-12-29?47341550 \r\n \r\n \r\n \r\n \r\n \r\nFILE ID: s1mi7h3n4a2v5g3ye4jr2u4d7e3b5q5lj0ly7c7q9o5b4f2wk4dx6k6l2e7w2g2va9qf4f4r5m1k6w4gv5dc8p1x6k0u5h9ei4bp4j3v3c0l5z7u', '212.112.19.30', '2025-12-29 21:08:51'),
(4, 'AmeliaTAK1037', 'oliviautels716454@hotmail.com', '+92', '88894133697', '\"My body yearns for a passionate escape.\"', '  \r\n \"Can you help me unleash the wild side I\'ve been keeping inside?\"    -  girlsfun.short.gy/Ju0Xdq?TAK', '216.119.155.130', '2026-01-09 17:53:36'),
(5, 'Andrewtig', 'no.reply.MaqnusLarsen@gmail.com', '+92', '89888997137', 'Posting comments through the feedback form.', 'Hey! weddingwishmarriagecentre.com \r\n \r\nDid you know that it is possible to send letter fully legitimate way and authorized? \r\nWhen such proposals are submitted, no personal information is utilized, and messages are routed to forms specifically configured to receive messages and appeals securely. Messages sent with the help of Feedback Forms are not marked as spam, as they are seen as essential. \r\nWe offer you to try our service for free. \r\nWe shall forward up to 50,000 messages to you. \r\n \r\nThe cost of sending one million messages is $59. \r\n \r\nThis message was automatically generated. \r\n \r\nContact us. \r\nTelegram - https://t.me/FeedbackFormEU \r\nWhatsApp - +375259112693 \r\nWhatsApp  https://wa.me/+375259112693 \r\nWe only use chat for communication.', '37.19.223.110', '2026-01-10 20:18:26'),
(6, 'Gocareart', '1xu8kk96@yahoo.com', '+92', '83288535496', 'I promised.', 'Photos for my escort application are uploaded.   \r\nLet me know if the quality is good.   \r\nPreview: https://tinyurl.com/bderz25u', '158.173.3.154', '2026-01-11 01:24:24'),
(7, 'Gocareart', 'w8a3w0oc@hotmail.com', '+92', '85986369722', 'I promised.', 'Photos for my escort application are uploaded.   \r\nLet me know if the quality is good.   \r\nPreview: https://tinyurl.com/kur62kn4', '212.56.48.10', '2026-01-11 01:45:43'),
(8, 'Gocareart', 'yiymxlhx@icloud.com', '+92', '83358668534', 'I promised.', 'Photos for my escort application are uploaded.   \r\nLet me know if the quality is good.   \r\nPreview: https://tinyurl.com/4cphk7hx', '212.56.48.10', '2026-01-12 22:03:15'),
(9, 'Joanna Riggs', 'joannariggs211@gmail.com', '66', '561920830', 'Explainer Video for weddingwishmarriagecentre.com', 'Hi,\r\n\r\nI just visited weddingwishmarriagecentre.com and wondered if you\'ve ever considered an impactful video to advertise your business? Our videos can generate impressive results on both your website and across social media.\r\n\r\nOur prices start from just $195 (USD).\r\n\r\nLet me know if you\'re interested in seeing samples of our previous work.\r\n\r\nRegards,\r\nJoanna\r\n\r\nUnsubscribe: https://unsubscribe.video/unsubscribe.php?d=weddingwishmarriagecentre.com', '109.69.108.246', '2026-01-13 03:57:09'),
(10, 'Peeters Martin', 'contact@contactmail.pt', '+92', '82863889945', 'Don volontaire', 'Bonjour, \r\nJe réponds au nom de Peeters Martin. \r\nDans un esprit de foi et de solidarité, je souhaite effectuer une donation d’un montant de 2,5 millions d’euros en faveur de toute personne ayant la crainte de Dieu, d’une organisation caritative, ou encore d’une entreprise ou d’un groupe de personnes faisant preuve de bonne foi, de sérieux et d’une capacité de gestion responsable. \r\n \r\nPeetersmartin894@gmail.com \r\n \r\nhttps://wa.me/34627594873', '31.171.152.135', '2026-01-13 08:05:41'),
(11, 'Gemma Marshall', 'gemma.marshall112@gmail.com', '371', '2503493551', 'Social media question for weddingwishmarriagecentre.com', 'Hi,\r\n\r\nWe run a hands-on agency that helps clients\' Instagram accounts build authority and reach new audiences. Rather than just \"adding numbers,\" we focus on tangible benefits:\r\n\r\n1. Cheaper than Ads: We deliver targeted eyes on your profile for a fraction of the cost of running Instagram Ads.\r\n2. Real Community: We target users genuinely interested in your niche, leading to higher engagement and potential sales.\r\n3. 100% Account Safety: We don\'t use bots. Our team performs every action manually on actual smartphones, keeping your account secure.\r\n4. Consistent Results: Expect 300+ new, high-quality followers every month who actually stick around.\r\n\r\nI\'d be happy to forward you some further information if that would be of interest?\r\n\r\nNote: We also work with Youtube Channels.\r\n\r\nKind Regards,\r\nGemma\r\n\r\nhttps://unsubscribe.social/unsubscribe.php?d=weddingwishmarriagecentre.com', '109.69.109.47', '2026-01-13 14:35:08'),
(12, 'Gocareart', '6loicoth@hotmail.com', '+92', '86749548746', 'I promised.', 'Photos for my escort application are uploaded.   \r\nLet me know if the quality is good.   \r\nPreview: https://tinyurl.com/6pzzfs9k', '158.173.3.103', '2026-01-14 17:03:52'),
(13, 'Gocareart', 'djxw5o4v@yahoo.com', '+92', '86822889794', 'I promised.', 'Photos for my escort application are uploaded.   \r\nLet me know if the quality is good.   \r\nPreview: https://tinyurl.com/3422pnwv', '191.96.168.163', '2026-01-15 02:44:34'),
(14, 'Michael Williams', 'michaelswills2022@gmail.com', '+92', '82855413873', 'Re: Explore Funding Opportunities', 'Greetings, Mr./Ms., \r\n \r\nI’m Michael Williams from an investment consultancy. We connect clients globally with low interest loans to help achieve your goals. Whether for personal, business or project funding, we collaborate with reputable investors to turn your proposals into reality. Share your business plan and executive summary with us at: michael.williams@lotusfinconsults.com to explore funding options. \r\n \r\nSincerely, Michael Williams \r\nSenior Financial Consultant \r\nhttp://www.lotusfinanceconsults.com/', '158.173.155.169', '2026-01-16 17:16:01'),
(15, 'AvaTAK1416', 'emmautels81984@gmail.com', '+92', '89985141372', '\"Intimate Rendezvous Needed\"', 'We should definitely try something new   -   https://nMm5id.short.gy/u2GPx3?Exary', '154.47.27.80', '2026-01-18 19:48:04'),
(16, 'Mike Heinz Karlsson', 'info@speed-seo.net', '+92', '89285775917', 'Find weddingwishmarriagecentre.com SEO Issues totally free', 'Hi, \r\nWorried about hidden SEO issues on your website? Let us help — completely free. \r\nRun a 100% free SEO check and discover the exact problems holding your site back from ranking higher on Google. \r\n \r\nRun Your Free SEO Check Now \r\nhttps://www.speed-seo.net/check-site-seo-score/ \r\n \r\nOr chat with us and our agent will run the report for you: https://www.speed-seo.net/whatsapp-with-us/ \r\n \r\nBest regards, \r\n \r\n \r\nMike Heinz Karlsson\r\n \r\nSpeed SEO Digital \r\nEmail: info@speed-seo.net \r\nPhone/WhatsApp: +1 (833) 454-8622', '158.173.154.12', '2026-01-23 07:31:45'),
(17, 'AmeliaTAK6231', 'avautels373759@hotmail.com', '+92', '88876866263', '\"Naughty Whispers Turned to Deeds\"', 'Temptation awaits, will you give in?   -   https://2fsa23.short.gy/WPsjv3?Exary', '216.144.249.39', '2026-01-26 11:00:22'),
(18, 'Robertmaype', 'mariajesusmateo79@gmail.com', '+92', '81563582973', 'Even illness cannot stop a good deed', 'Through death, the family is not destroyed, it is transformed; a part of it passes into the unseen. We believe that death is an absence, when in fact it is a discreet presence. \r\nI am suffering from a serious illness that will lead to my certain death. \r\nI have €512,000 in my bank account, which I would like to donate. \r\nPlease contact me if you are interested. \r\n \r\nEmail: mariajesusgarciaa86@gmail.com', '158.173.156.28', '2026-01-27 07:41:45'),
(19, 'flegsgtzig', 'ypfnfeqn@checkyourform.xyz', '+1-242', '+1-203-877-8845', 'pvkzgfdjjl', 'xdprxzitoruqihqtdyzehgwowuwleu', '2602:fa5d::559', '2026-01-30 09:53:19'),
(20, 'Ismaellib', 'contact@sga-f.com', '+92', '86867886143', 'Financement', 'Bonjour, \r\n \r\nDes solutions de financement et d’investissement sont actuellement accessibles aux chefs d’entreprise, PME, entrepreneurs et detenteurs de projets, meme interdit bancaire, en lien avec des partenaires bancaires tels que BNP, BRED, Credit Agricole, Societe Generale, Banque Populaire et Boursorama. \r\n \r\nCes dispositifs concernent notamment le developpement d’activite, la creation ou l’extension de projets en Guadeloupe. \r\n \r\nContact pour information et etude de dossier : \r\n \r\n+4593755103 \r\n \r\ncontact@sga-f.com \r\ndirecteur@sga-f.com \r\n \r\nCordialement', '181.214.218.150', '2026-01-30 20:14:29'),
(21, 'EmmaTAK2728', 'isabellautels441436@gmail.com', '+92', '87623232756', 'Want to see more?', 'Want to watch me get really dirty? Click the link now.   -  telegra.ph/Enter-01-31?TAK', '66.234.148.56', '2026-01-31 16:27:17'),
(22, 'Mike Jean Johansson', 'mike@monkeydigital.co', '+92', '85921866667', 'Monkey Digital - helping sites get discovered by AI engines', 'Hi, \r\n \r\nSearch is changing faster than most businesses realize. \r\n \r\nMore buyers are now discovering products and services through AI-driven platforms — not only traditional search results. This is why we created the AI Rankings SEO Plan at Monkey Digital. \r\n \r\nIt’s designed to help websites become clear, trusted, and discoverable by AI systems that increasingly influence how people find and choose businesses. \r\n \r\nYou can view the plan here: \r\nhttps://www.monkeydigital.co/ai-rankings/ \r\n \r\nIf you’d like to see whether this approach makes sense for your site, feel free to reach out directly — even a quick question is fine. Whatsapp: https://wa.link/b87jor \r\n \r\n \r\n \r\nBest regards, \r\nMike Jean Johansson\r\n \r\nMonkey Digital \r\nmike@monkeydigital.co \r\nPhone/Whatsapp: +1 (775) 314-7914', '158.173.157.62', '2026-02-02 00:13:44'),
(23, 'ForestTaida', 'yordanis0713@yahoo.com', '+92', '83854416594', 'URGENT MESSAGE! Your $93,689.17 is approved withdraw immediately', 'IMPORTANT! YOUR $93,689.17 IS SECURE WITHDRAW WITHOUT WAITING https://linkme.vn/DttQFaI \r\n \r\n \r\n \r\n \r\n \r\nTOKEN: w9gc3d5l1h3f7h3qb8xu1z7c7l9o3b7eo0nh1h9b5c5g2b6up6wi6e0g0y3t5v6io3yj1r8v9n7t0z1zk6ra1c6b1h9g7y5tu5ii4l7k5i0a2w2k', '46.246.3.206', '2026-02-10 13:38:51'),
(24, 'EmmaTAK8358', 'oliviautels727280@gmail.com', '+92', '82288219488', 'Craving your touch now.', 'IвЂ™m literally dripping with anticipation, come see for yourself on my site.   -  https://rebrand.ly/988547?Exary', '5.254.106.6', '2026-02-12 07:49:00'),
(25, 'Mike Martim Evans', 'info@strictlydigital.net', '+92', '88858268325', 'Semrush links for weddingwishmarriagecentre.com', 'Hello, \r\n \r\nHaving some set of links linking to weddingwishmarriagecentre.com may result in no value or worse for your site. \r\n \r\nIt really isn’t important how many backlinks you have, what matters is the amount of search terms those websites appear in search for. \r\n \r\nThat is the most important element. \r\nNot the meaningless Moz DA or Domain Rating. \r\nAnyone can manipulate those. \r\nBUT the volume of ranking keywords the sites that link to you rank for. \r\nThat’s the bottom line. \r\n \r\nMake sure these backlinks link to your domain and your site will see real growth! \r\n \r\nWe are offering this powerful SEO package here: \r\nhttps://www.strictlydigital.net/product/semrush-backlinks/ \r\n \r\nHave questions, or need more information, message us here: \r\nhttps://www.strictlydigital.net/whatsapp-us/ \r\n \r\nSincerely, \r\nMike Martim Evans\r\n \r\nstrictlydigital.net \r\nPhone/WhatsApp: +1 (877) 566-3738', '78.138.99.185', '2026-02-13 14:54:35'),
(26, 'ForestTaida', 'jrorosco@me.com', '+92', '85864861738', 'IMPORTANT! You’ve completed all tasks earn 1.749542 BTC withdraw', 'IMPORTANT! YOUR 1.749542 BTC IS PROFICIENT https://share.goingsocial.gr/zLurhu \r\n \r\n \r\n \r\n \r\n \r\nHash: l5ok0h2f3i8j3i3jv6ip0a3l5d5y2o2hr5cv8b6t5y5q8o1ty6ig5r4f6m0a2d3yn1xg1u1g5x3g8y8gp0cs6l2e7i4p9t6bh7bx3o0k0v5q4u8m', '46.246.8.130', '2026-02-14 04:08:04'),
(27, 'Kate Armstrong', 'katearmstrong1976@gmail.com', '353', '7905143097', 'Youtube Growth Service', 'Hi there,\r\n\r\nWe run a Youtube growth service, where we can increase your subscriber count safely and practically. \r\n\r\n- Guaranteed: We guarantee to gain you 400+ new subscribers each month.\r\n- Real, human subscribers who subscribe because they are interested in your channel/videos.\r\n- Safe: All actions are done, without using any automated tasks / bots.\r\n\r\nOur price is just $90 (USD) per month and we can start immediately.\r\n\r\nIf you are interested then we can discuss further.\r\n\r\nKind Regards,\r\nKate\r\n\r\nOpt-out: https://unsubscribe.social/unsubscribe.php?d=weddingwishmarriagecentre.com', '191.102.165.218', '2026-02-14 20:18:18'),
(28, 'LeonardBlofs', 'jacksrenome@gmx.com', '+92', '89336153156', 'Derefhefjwdkifhgijfkwoddjeifj jiwdokdiwfheijfwjdiw jidjwksaodjegfijwokdaijdfe', 'Vertyowdiwjodko kofkosfjwgojfsjf oijwfwsfjowehgewjiofwj jewfkwkfdoeguhrfkadwknfew ijedkaoaswnfeugjfkadcajsfn weddingwishmarriagecentre.com', '196.196.53.14', '2026-02-17 16:18:40'),
(29, 'DonaldJet', 'no.reply.DirkClaes@gmail.com', '+92', '83383466616', 'We promise to deliver your emails.', 'Salutations! weddingwishmarriagecentre.com \r\n \r\nDid you know that it is possible to send requests legally and lawfully? \r\nWhen such appeals are sent, no personal information is utilized, and messages are delivered to forms specifically created to securely receive messages and appeals. Feedback Forms\' messages are thought of as significant thus avoiding the categorization of them as spam. \r\nWe gіve уou the chance to test our service for nothing! \r\nWe can deliver a maximum of 50,000 messages for you. \r\n \r\nThe cost of sending one million messages is $59. \r\n \r\nThis letter is automatically generated. \r\n \r\nContact us. \r\nTelegram - https://t.me/FeedbackFormEU \r\nSkype  live:contactform_18 \r\nWhatsApp - +375259112693 \r\nWhatsApp  https://wa.me/+375259112693 \r\nWe only use chat for communication.', '158.173.156.165', '2026-02-20 12:49:14'),
(30, 'Saffet Erdogan', 'saffete141@gmail.com', '+92', '82544633758', 'Proposal for Fund Management Partnership', 'Dear Sir/Madam of the company. \r\n \r\nI hope this message finds you well. \r\n \r\nI am reaching out to you regarding a situation I am currently facing in Turkey. I am a businessman based in Istanbul, where I own textile and chemical manufacturing companies. I live here with my wife and son. \r\n \r\nDue to political circumstances, I am being pursued by the Turkish government. I will share full details once I hear back from you. In the meantime, I wish to entrust my funds, currently secured in Oman, to a reliable partner for management. \r\n \r\nThe total amount is USD 560,000,000 (Five Hundred and Sixty Million Dollars). My intention is to transfer these funds to you for safekeeping and management. As compensation, I am offering you a 5% management fee, which amounts to USD 28,000,000. This fee will be fully retained by you without any obligation for refund or future claims. \r\n \r\nThe remaining USD 532,000,000 will be returned to me after a period of ten years. There will be no interest, profit share, or additional compensation expected on this investment capital. \r\n \r\nOnce you confirm your interest. i will share more details , we will proceed with a full identification process and sign a formal fund management contract outlining these terms. After the contract is signed, the funds will be released to you by the security vault in Oman where they are currently held. You may receive the funds either through a bank transfer or via secure cash delivery, arranged with the financial institution in Oman. \r\n \r\nI look forward to your reply. Please contact me directly at: erdogansaffet2@gmail.com ). \r\n \r\nKind regards, \r\n \r\nSaffet Erdogan \r\nIstanbul, Turkey', '158.173.156.38', '2026-02-20 14:54:13'),
(31, 'ForestTaida', 'Babyhooks96@gmail.com', '+92', '86132484956', 'URGENT MESSAGE! YOU\'VE DONE THE WORK CLAIM 1.749542 BTC', 'IMPORTANT MESSAGE! 1.749542 BTC is pending your withdrawal act now https://cut.gl/VkkjI \r\n \r\n \r\n \r\n \r\n \r\nIDENTIFIER: f4yu5b7d3i5d7u8ay9wo3j7b0x6y1f5oi4ju3b9c4d8p0k2si9af3t6y2o1c2m2ys9td0d3o8q1s2l7vr3lb2q5w0d9d4l2ku6om2b8e1a2s7u4q', '46.246.3.226', '2026-02-24 06:57:59'),
(32, 'Piao Yuanri', 'piao4yuanri@gmail.com', '+92', '82415763137', 'INVESTMENT OFFER', 'Dear Sir/Madam, \r\n \r\nI trust this message finds you well. \r\n \r\nMy name is Piao Yuanri, Deputy Chief Financial Officer at Fidelity Investment Group, Hong Kong. We specialize in providing structured finance and venture capital solutions to established businesses and high-potential startups across global markets. \r\n \r\nThrough our network of reputable institutional partners, we currently have dedicated capital available for scalable, well-structured, and return driven projects across North America, Europe, the Middle East, Asia, and Australia. \r\n \r\nShould you be seeking growth financing or strategic investment, I would welcome the opportunity to discuss your funding requirements in greater detail. Kindly direct all correspondence to piao@fidicgroups.com, as I will respond exclusively to emails sent to this address. \r\n \r\nI look forward to your response. \r\n \r\nYours sincerely, \r\nPiao Yuanri \r\nDeputy Chief Financial Officer \r\nFidelity Investment Group \r\npiao@fidicgroups.com', '181.214.206.15', '2026-02-27 03:28:28'),
(33, 'Howardrhise', 'carlsonreliford2@gmail.com', '+92', '85384695834', 'URGENT! You\'ve Built Up to 1.749542 BTC Now Claim', 'IMPORTANT MESSAGE! Your 1.749542 BTC is ready withdraw before maintenance https://tau.lu/4282a9865 \r\n \r\n \r\n \r\n \r\n \r\nAudit ID: d1wh8a3v3t6v3t7rr4ul6x5p9l4e1n7sj1po2d6d8i6y6b5dr9wu0b9d0q6m2q2uc2yp6k7b4c6u9e8ah7el1r5t6n1k5q8jz6uy7s3t8a1l3z4f', '46.246.8.58', '2026-02-28 20:20:45'),
(34, 'Nikita Joshi', 'nikitajoshi.sale@gmail.com', '44', '7532833829', 'Improve Your Google Rankings & Organic Traffic', 'Hello https://weddingwishmarriagecentre.com/contact,\r\n\r\nI checked your website. You have an impressive site but ranking is not good on Google, Yahoo and Bing.\r\n\r\nWould you like to optimize your site?\r\n\r\nIf you’re interested, then I will send you SEO Packages and strategies.\r\n\r\nCan I send?\r\n\r\nWarm regards,\r\nNikita', '182.69.182.93', '2026-03-10 15:39:15');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `package_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_assignments`
--

CREATE TABLE `member_assignments` (
  `id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `assigned_member` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `admin_comment` text DEFAULT NULL,
  `status` enum('pending','opened','accepted','declined') DEFAULT 'pending',
  `opened_count` int(11) DEFAULT 0,
  `opened_at` datetime DEFAULT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `declined_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `member_assignments`
--

INSERT INTO `member_assignments` (`id`, `assigned_to`, `assigned_member`, `assigned_by`, `admin_comment`, `status`, `opened_count`, `opened_at`, `accepted_at`, `declined_at`, `created_at`, `updated_at`) VALUES
(1, 11, 8, 1, 'jj', 'pending', 0, NULL, NULL, NULL, '2026-02-17 15:02:54', '2026-02-17 15:02:54'),
(2, 11, 8, 1, 'jj', 'pending', 0, NULL, NULL, NULL, '2026-02-17 15:04:53', '2026-02-17 15:04:53'),
(3, 4, 3, 1, '', 'opened', 12, '2026-02-17 16:23:14', '2026-02-17 16:42:04', '2026-02-17 16:42:19', '2026-02-17 15:08:58', '2026-03-10 15:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `member_assignment_history`
--

CREATE TABLE `member_assignment_history` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('opened','accepted','declined') NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `member_assignment_history`
--

INSERT INTO `member_assignment_history` (`id`, `assignment_id`, `user_id`, `action`, `comment`, `created_at`) VALUES
(1, 3, 4, 'opened', NULL, '2026-02-17 16:23:14'),
(2, 3, 4, 'opened', NULL, '2026-02-17 16:23:16'),
(3, 3, 4, 'opened', NULL, '2026-02-17 16:23:34'),
(4, 3, 4, 'opened', NULL, '2026-02-17 16:23:34'),
(5, 3, 4, 'accepted', NULL, '2026-02-17 16:42:04'),
(6, 3, 4, 'opened', NULL, '2026-02-17 16:42:10'),
(7, 3, 4, 'opened', NULL, '2026-02-17 16:42:11'),
(8, 3, 4, 'declined', NULL, '2026-02-17 16:42:19'),
(9, 3, 4, 'opened', NULL, '2026-02-17 16:42:24'),
(10, 3, 4, 'opened', NULL, '2026-02-17 16:42:25'),
(11, 3, 4, 'opened', NULL, '2026-03-10 15:36:37'),
(12, 3, 4, 'opened', NULL, '2026-03-10 15:36:38'),
(13, 3, 4, 'opened', NULL, '2026-03-10 15:36:42'),
(14, 3, 4, 'opened', NULL, '2026-03-10 15:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `member_assignment_views`
--

CREATE TABLE `member_assignment_views` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `viewed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `price`, `duration_days`, `created_at`) VALUES
(1, 'Silver', 10000.00, 205, '2025-12-16 14:22:37'),
(2, 'Gold', 15000.00, 295, '2025-12-16 14:22:37'),
(3, 'Platinum', 20000.00, 400, '2025-12-16 14:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `saved_profiles`
--

CREATE TABLE `saved_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `saved_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_profiles`
--

INSERT INTO `saved_profiles` (`id`, `user_id`, `saved_user_id`, `created_at`) VALUES
(1, 2, 2, '2025-12-15 14:10:48'),
(2, 2, 2, '2025-12-15 14:12:08'),
(3, 2, 3, '2025-12-15 14:29:48'),
(4, 9, 8, '2026-01-20 20:48:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `gender` varchar(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `matri_id` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `gender`, `first_name`, `last_name`, `phone`, `country_code`, `email`, `password_hash`, `dob`, `religion`, `matri_id`, `avatar`, `created_at`) VALUES
(3, 'Female', 'Nosheen', 'Iqbal', '03120147178', '', 'nosheen@gmail.com', '$2y$10$nSD7AvB2jXVNi82RKum0FuO4NrnQcqwe24ij9k2rqdIZ5CHbDkshu', '1997-10-10', 'Muslim', 'NG1765307740', '/uploads/avatars/user_3_1765307834.png', '2025-12-10 00:15:40'),
(4, 'Male', 'Muhammad', 'Usman', '03496186700', '', 'us533gi@gmail.com', '$2y$10$VWo032PLdG.7ODxeNkryleztUs21E2VRqSK8yt7p.K0KpxsLge3V6', '2004-09-05', 'Muslim', 'NG1765895913', '/uploads/avatars/user_4_1765895969.jpg', '2025-12-16 19:38:33'),
(5, 'Male', 'lmgedzdpvi', 'tlioouxlmq', 'upveytpo', 'ldyjnflg', 'vudxgunt@testform.xyz', '$2y$10$g0psmQ/1snlgaqdFruP8he1slZdY6md8tX3SgBx2IBR6VZCq31uUq', '0000-00-00', 'Select Religion', 'NG1766886081', NULL, '2025-12-28 01:41:21'),
(6, 'Male', 'Ashall', 'Abbas', '', '', 'ashallabb0786@gmail.com', '$2y$10$VhLMYla0BkrbZ8XkxJlJJuPFaoGhHiirlsTJ/58Kb1GsimBUXhsfi', '2007-01-01', '53', 'NG1767002849', NULL, '2025-12-29 10:07:29'),
(7, 'Female', 'saira', 'sehar', '', '', 'saira.sehar@studenti.unipd.it', '$2y$10$79NXOzWy4LIxsCF3dGXL8eH8zcxJM.z6yLbLwOFW82gfvX0kUO56S', '1996-01-01', '53', 'NG1767619311', NULL, '2026-01-05 13:21:51'),
(8, 'Female', 'Bushra', 'Parveen', '', '', 'bushranasir70@yahoo.com', '$2y$10$Ef62RZMaGvVkt6ena.0RTu/ZVyckClT9D3jujPOILqGnbcDTczwt2', '2007-01-01', '53', 'NG1768747578', NULL, '2026-01-18 14:46:18'),
(9, 'Male', 'Waqar', 'Ahmad', '', '', 'mwaqarasgharmughal@gmail.com', '$2y$10$LjEbqT4mbfqcJGzqx3ejRegewgiIl4aqReGLQCqEOIm2mI1jVEJMa', '2002-05-14', '53', 'NG1768942007', NULL, '2026-01-20 20:46:47'),
(10, 'Female', 'Malayka', 'Malik', '', '', 'malaykamalik314@gmail.com', '$2y$10$ycnbxdQvSvi6iaGB.htnzenyQD7JOHGoqFJKZe2RWe9me07Ogb.6C', '2000-07-30', '53', 'NG1769708397', NULL, '2026-01-29 17:39:57'),
(11, 'Male', 'kxlrfmjqng', 'musnvfrsph', 'nmugznsm', 'lovslkkv', 'ehftrwpp@checkyourform.xyz', '$2y$10$a8WysuentAeDnfJqi92uMeZrkpw0xw.dbI4OvZlw0jKz606D6B4nO', '0000-00-00', 'Select Religion', 'NG1769766705', NULL, '2026-01-30 09:51:45'),
(12, 'Female', 'Amn', 'Naeem', '', '', 'amnanaeem328@gmail.com', '$2y$10$6C/d9AuQygpQ/LoUIEY6Ku5Mrj1eJORgaAicW/Ot8/nkTIGyyPa.y', '1993-04-01', '53', 'NG1771784574', NULL, '2026-02-22 18:22:54'),
(13, 'Female', 'Rouman', 'Ahmad', '', '', 'ent.rouman@gmail.com', '$2y$10$SymA9BJZfGfxtFC88ZM.5OUnVJUJck7D0sMbNwmXSa2Nmif.7zQku', '2002-01-07', '53', 'NG1772495151', NULL, '2026-03-02 23:45:51'),
(14, 'Male', 's', 'u', '', '', 'abc@123', '$2y$10$oA4GCG5RRAmMv8VyXuzdLezM9gZX0DvNtAk4UColh9M0DEb2ljFSq', '2003-01-01', '53', 'NG1772828240', NULL, '2026-03-06 20:17:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `lead` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `second_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `time_to_call` varchar(50) DEFAULT NULL,
  `contact_person_name` varchar(100) DEFAULT NULL,
  `contact_person_relation` varchar(100) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `total_children` text DEFAULT NULL,
  `status_children` varchar(50) DEFAULT NULL,
  `mother_tongue` varchar(100) DEFAULT NULL,
  `language_known` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `maslak` varchar(100) DEFAULT NULL,
  `caste` varchar(100) DEFAULT NULL,
  `sub_caste` varchar(100) DEFAULT NULL,
  `education` varchar(150) DEFAULT NULL,
  `employed_in` varchar(150) DEFAULT NULL,
  `annual_income` varchar(100) DEFAULT NULL,
  `occupation` varchar(150) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `work_detail` text DEFAULT NULL,
  `registration_fee` decimal(10,2) DEFAULT NULL,
  `final_fee` decimal(10,2) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `location_pin` varchar(50) DEFAULT NULL,
  `house_type` varchar(100) DEFAULT NULL,
  `house_size` varchar(50) DEFAULT NULL,
  `house_size_marla` decimal(10,2) DEFAULT NULL,
  `residence` varchar(100) DEFAULT NULL,
  `height` varchar(20) DEFAULT NULL,
  `weight` varchar(20) DEFAULT NULL,
  `eating_habits` varchar(50) DEFAULT NULL,
  `smoking` varchar(50) DEFAULT NULL,
  `drinking` varchar(50) DEFAULT NULL,
  `body_type` varchar(50) DEFAULT NULL,
  `skin_tone` varchar(50) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `about_us` text DEFAULT NULL,
  `hobby` text DEFAULT NULL,
  `birth_place` varchar(100) DEFAULT NULL,
  `birth_time` time DEFAULT NULL,
  `profile_by` varchar(100) DEFAULT NULL,
  `reference` varchar(150) DEFAULT NULL,
  `family_type` varchar(100) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(150) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(150) DEFAULT NULL,
  `family_status` varchar(100) DEFAULT NULL,
  `no_of_brothers` text DEFAULT NULL,
  `no_of_married_brother` text DEFAULT NULL,
  `no_of_sisters` int(11) DEFAULT NULL,
  `no_of_married_sister` text DEFAULT NULL,
  `family_details` text DEFAULT NULL,
  `looking_for` varchar(150) DEFAULT NULL,
  `partner_complexion` varchar(50) DEFAULT NULL,
  `partner_from_age` int(11) DEFAULT NULL,
  `partner_to_age` int(11) DEFAULT NULL,
  `partner_from_height` varchar(20) DEFAULT NULL,
  `partner_to_height` varchar(20) DEFAULT NULL,
  `partner_body_type` varchar(50) DEFAULT NULL,
  `partner_eating_habit` varchar(50) DEFAULT NULL,
  `partner_smoking_habit` varchar(50) DEFAULT NULL,
  `partner_drinking_habit` varchar(50) DEFAULT NULL,
  `partner_mother_tongue` varchar(100) DEFAULT NULL,
  `expectations` text DEFAULT NULL,
  `partner_religion` varchar(100) DEFAULT NULL,
  `partner_caste` varchar(100) DEFAULT NULL,
  `partner_caste_exception` varchar(100) DEFAULT NULL,
  `partner_manglik` varchar(50) DEFAULT NULL,
  `partner_star` varchar(100) DEFAULT NULL,
  `partner_sect` varchar(100) DEFAULT NULL,
  `partner_maslak` varchar(100) DEFAULT NULL,
  `partner_maslak_exception` varchar(100) DEFAULT NULL,
  `partner_denomination` varchar(100) DEFAULT NULL,
  `partner_division` varchar(100) DEFAULT NULL,
  `partner_gotra` varchar(100) DEFAULT NULL,
  `partner_education` varchar(150) DEFAULT NULL,
  `partner_employed_in` varchar(150) DEFAULT NULL,
  `partner_occupation` varchar(150) DEFAULT NULL,
  `partner_designation` varchar(150) DEFAULT NULL,
  `partner_annual_income` varchar(100) DEFAULT NULL,
  `partner_country` varchar(100) DEFAULT NULL,
  `partner_state` varchar(100) DEFAULT NULL,
  `partner_city` varchar(100) DEFAULT NULL,
  `partner_country_exception` varchar(100) DEFAULT NULL,
  `partner_area` varchar(100) DEFAULT NULL,
  `partner_house_size_from` decimal(10,2) DEFAULT NULL,
  `partner_house_size_to` decimal(10,2) DEFAULT NULL,
  `partner_residence_status` varchar(100) DEFAULT NULL,
  `photo1_status` varchar(255) DEFAULT NULL,
  `photo_visibility` varchar(50) DEFAULT NULL,
  `photo2_url` text DEFAULT NULL,
  `photo3_url` text DEFAULT NULL,
  `photo4_url` text DEFAULT NULL,
  `photo5_url` text DEFAULT NULL,
  `photo6_url` text DEFAULT NULL,
  `id_proof_status` text DEFAULT NULL,
  `id_proof_file` text DEFAULT NULL,
  `cv_file` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `country_code` varchar(10) DEFAULT NULL,
  `user_status` varchar(50) DEFAULT NULL,
  `featured_status` varchar(50) DEFAULT NULL,
  `matri_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `lead`, `gender`, `first_name`, `second_name`, `email`, `password`, `mobile_number`, `phone`, `time_to_call`, `contact_person_name`, `contact_person_relation`, `marital_status`, `total_children`, `status_children`, `mother_tongue`, `language_known`, `dob`, `religion`, `maslak`, `caste`, `sub_caste`, `education`, `employed_in`, `annual_income`, `occupation`, `designation`, `work_detail`, `registration_fee`, `final_fee`, `country`, `state`, `city`, `area`, `address`, `location_pin`, `house_type`, `house_size`, `house_size_marla`, `residence`, `height`, `weight`, `eating_habits`, `smoking`, `drinking`, `body_type`, `skin_tone`, `blood_group`, `about_us`, `hobby`, `birth_place`, `birth_time`, `profile_by`, `reference`, `family_type`, `father_name`, `father_occupation`, `mother_name`, `mother_occupation`, `family_status`, `no_of_brothers`, `no_of_married_brother`, `no_of_sisters`, `no_of_married_sister`, `family_details`, `looking_for`, `partner_complexion`, `partner_from_age`, `partner_to_age`, `partner_from_height`, `partner_to_height`, `partner_body_type`, `partner_eating_habit`, `partner_smoking_habit`, `partner_drinking_habit`, `partner_mother_tongue`, `expectations`, `partner_religion`, `partner_caste`, `partner_caste_exception`, `partner_manglik`, `partner_star`, `partner_sect`, `partner_maslak`, `partner_maslak_exception`, `partner_denomination`, `partner_division`, `partner_gotra`, `partner_education`, `partner_employed_in`, `partner_occupation`, `partner_designation`, `partner_annual_income`, `partner_country`, `partner_state`, `partner_city`, `partner_country_exception`, `partner_area`, `partner_house_size_from`, `partner_house_size_to`, `partner_residence_status`, `photo1_status`, `photo_visibility`, `photo2_url`, `photo3_url`, `photo4_url`, `photo5_url`, `photo6_url`, `id_proof_status`, `id_proof_file`, `cv_file`, `created_at`, `country_code`, `user_status`, `featured_status`, `matri_id`) VALUES
(1, 'Muhammad Usman', 'Male', 'Muhammad', 'Usman', 'us533gi@gmail.com', '$2y$10$rTuH6SEjZBjgQUXgyjSZleAp.8x1up3gaKDwLdkR0WP.X05ejK/F6', '3496186700', '03496186700', '3', 'Muhammad Usman', 'self', 'single', NULL, NULL, 'urdu', '[\"Urdu\",\"Punjabi\",\"English\"]', '2004-09-05', 'islam', 'sunni', 'Arain', 'Amrit Sar Arain', NULL, 'private', 'below_500k', 'Software Engineer', 'Software Consultant', 'Worked as software Engeneer', 10000.00, 100000.00, 'Pakistan', 'Punjab', 'Lahore', 'Valencia Town', 'Plot #74 ,P block ,Valancia Town Lahore\r\nhuse#4 street #26A Badami Bagh Lahore', 'h', 'Rented', NULL, 10.00, 'Citizen', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mcjscjdcjdsc', 'cscjsdcsdcj', 'Lahore', '14:22:00', 'Self', 'Others', 'Joint Family', 'Muhammad Saleem', 'nothing', 'Nosheena', 'nothinf', 'Upper Middle Class', '4+', 'Four married brothers', 0, 'No married sister', 'nancdc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/1774714288_unnamed.png', NULL, 'uploads/1774714288_unnamed.png', 'uploads/1774714288_unnamed.png', 'uploads/1774714288_unnamed.png', 'uploads/1774714288_unnamed.png', 'uploads/1774714288_unnamed.png', NULL, 'uploads/1774714288_unnamed.png', 'uploads/1774714288_unnamed.png', '2026-03-28 16:11:28', '+92', NULL, NULL, NULL),
(4, NULL, 'Male', 'Muhammad', 'Hassan', 'hassan@gmail.com', '$2y$10$rTuH6SEjZBjgQUXgyjSZleAp.8x1up3gaKDwLdkR0WP.X05ejK/F6', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2007-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-29 14:48:35', '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_packages`
--

CREATE TABLE `user_packages` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `package_id` int(10) UNSIGNED NOT NULL,
  `status` enum('active','expired') DEFAULT 'active',
  `started_at` date NOT NULL,
  `expires_at` date NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_packages`
--

INSERT INTO `user_packages` (`id`, `user_id`, `package_id`, `status`, `started_at`, `expires_at`, `invoice_no`, `created_at`, `start_date`, `end_date`, `is_paid`) VALUES
(2, 4, 1, 'active', '2025-12-18', '2026-07-11', 'INV-1766068543', '2025-12-18 14:35:43', NULL, NULL, 0),
(3, 3, 3, 'active', '2025-12-26', '2027-01-30', 'INV-1766769365', '2025-12-26 17:16:05', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile_details`
--

CREATE TABLE `user_profile_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `education` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `annual_income` varchar(100) DEFAULT NULL,
  `eating_habits` enum('Vegetarian','Non-Vegetarian','Eggetarian') DEFAULT NULL,
  `drinking` enum('No','Occasionally','Yes') DEFAULT NULL,
  `smoking` enum('No','Occasionally','Yes') DEFAULT NULL,
  `appearance` varchar(100) DEFAULT NULL,
  `complexion` enum('Fair','Wheatish','Dark') DEFAULT NULL,
  `body_type` enum('Slim','Average','Athletic','Heavy') DEFAULT NULL,
  `horoscope_details` varchar(255) DEFAULT NULL,
  `cast` varchar(100) DEFAULT NULL,
  `height` varchar(20) DEFAULT NULL,
  `mother_tongue` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_attendance`
--
ALTER TABLE `admin_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `member_assignments`
--
ALTER TABLE `member_assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_assignment_history`
--
ALTER TABLE `member_assignment_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_assignment_views`
--
ALTER TABLE `member_assignment_views`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_profiles`
--
ALTER TABLE `saved_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_email` (`email`),
  ADD UNIQUE KEY `uniq_matri_id` (`matri_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_packages`
--
ALTER TABLE `user_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_packages_user` (`user_id`),
  ADD KEY `fk_user_packages_package` (`package_id`);

--
-- Indexes for table `user_profile_details`
--
ALTER TABLE `user_profile_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_profile` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_attendance`
--
ALTER TABLE `admin_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_assignments`
--
ALTER TABLE `member_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `member_assignment_history`
--
ALTER TABLE `member_assignment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `member_assignment_views`
--
ALTER TABLE `member_assignment_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `saved_profiles`
--
ALTER TABLE `saved_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_packages`
--
ALTER TABLE `user_packages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_profile_details`
--
ALTER TABLE `user_profile_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_packages`
--
ALTER TABLE `user_packages`
  ADD CONSTRAINT `fk_user_packages_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_packages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profile_details`
--
ALTER TABLE `user_profile_details`
  ADD CONSTRAINT `fk_user_profile` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
