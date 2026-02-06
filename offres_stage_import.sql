-- =====================================================
-- Base de données EducaVision - Table Offres de Stage
-- =====================================================

-- Créer la table offre_stage
CREATE TABLE IF NOT EXISTS `offre_stage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `entreprise` varchar(255) NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `duree_jours` int(11) NOT NULL,
  `url_candidature` varchar(255) DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'Ouvert',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Données d'exemple pour tester le CRUD
-- =====================================================

INSERT INTO `offre_stage` (`id`, `titre`, `description`, `entreprise`, `lieu`, `date_debut`, `date_fin`, `duree_jours`, `url_candidature`, `date_creation`, `statut`) VALUES
(1, 'Développeur Web Junior', 'Nous recherchons un développeur web junior pour rejoindre notre équipe. Vous travaillerez sur des projets PHP/Symfony avec une équipe bienveillante. Missions principales : développement de nouvelles fonctionnalités, maintenance du code existant, participation aux réunions d\'équipe.', 'Acme Corporation', 'Paris, France', '2026-02-15 09:00:00', '2026-05-15 17:00:00', 90, 'https://acme-corp.fr/candidature', '2026-02-03 10:30:00', 'Ouvert'),

(2, 'Data Analyst', 'Analyse et visualisation de données avec Python et Tableau. Vous analyserez les données clients, créerez des rapports automatisés et proposerez des optimisations. Experience avec SQL requise.', 'TechData Solutions', 'Lyon, France', '2026-03-01 09:00:00', '2026-06-01 17:00:00', 92, 'https://techdata.com/jobs/stage', '2026-02-03 10:15:00', 'Ouvert'),

(3, 'Développeur Mobile React Native', 'Créez des applications mobiles pour iOS et Android. Nous utilisons React Native et vous travaillerez sur une application de e-commerce. Connaissance JavaScript/TypeScript essentielle.', 'MobileApp Inc', 'Bordeaux, France', '2026-02-20 09:00:00', '2026-05-20 17:00:00', 90, 'https://mobileapp.fr/apply', '2026-02-03 09:45:00', 'Ouvert'),

(4, 'Designer UX/UI', 'Nous recherchons un designer UX/UI pour refondre l\'interface utilisateur de nos produits. Vous utiliserez Figma et travaillerez en étroite collaboration avec notre équipe produit. Portfolio requis.', 'Creative Studio', 'Toulouse, France', '2026-03-15 09:00:00', '2026-06-15 17:00:00', 92, 'https://creativestudio.com/stage', '2026-02-02 14:20:00', 'Ouvert'),

(5, 'Assistant DevOps', 'Support du déploiement et maintenance d\'infrastructure cloud (AWS/Azure). Vous apprendrez Docker, Kubernetes, CI/CD pipelines avec une équipe expérimentée.', 'CloudTech Consulting', 'Paris, France', '2026-02-01 09:00:00', '2026-05-01 17:00:00', 90, NULL, '2026-02-01 08:00:00', 'Fermé'),

(6, 'Chef de projet Digital', 'Coordination de projets web et mobile. Gestion des plannings, communication avec les clients et suivi de la qualité. Excellent relationnel demandé.', 'Digital Agency Pro', 'Marseille, France', '2026-04-01 09:00:00', '2026-07-01 17:00:00', 91, 'https://agency-pro.fr/recrutement', '2026-02-03 11:00:00', 'Ouvert'),

(7, 'Spécialiste Marketing Digital', 'SEO/SEM, réseaux sociaux, analytics. Créez et optimisez des campagnes marketing numériques. Connaissance de Google Ads et GA4 souhaitée.', 'MarketingMaxx', 'Lille, France', '2026-03-10 09:00:00', '2026-06-10 17:00:00', 92, 'https://marketingmaxx.fr/candidature', '2026-02-03 10:00:00', 'Pourvu'),

(8, 'Testeur QA Automatisé', 'Création et exécution de tests automatisés avec Selenium/Cypress. Vous assurez la qualité des produits logiciels avant mise en production.', 'QualityFirst', 'Nantes, France', '2026-02-10 09:00:00', '2026-05-10 17:00:00', 90, 'https://qualityfirst.fr/stage', '2026-02-03 09:30:00', 'Ouvert');

-- =====================================================
-- Vérifier les données importées
-- =====================================================
SELECT * FROM `offre_stage` ORDER BY `date_creation` DESC;
