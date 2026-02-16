<?php
    namespace app\models;
    class ProductModel {
	private $db;
	public function __construct($db)
	{
		$this->db = $db;
	}
	public function getLivraison() {
		$stmt = $this->db->query("SELECT l.id,
       l.date_livraison,
       s.nom AS statut,
       c.description AS colis,
       ch.nom AS chauffeur,
       v.immatriculation
FROM livraison l
JOIN statut s ON l.id_statut = s.id
JOIN colis c ON l.id_colis = c.id
JOIN chauffeur ch ON l.id_chauffeur = ch.id
JOIN vehicule v ON l.id_vehicule = v.id;
");
		return $stmt->fetchAll();
	}
    // public function getProduitById($id) {
	// 	$stmt = $this->db->query("SELECT * FROM PRODUITS WHERE id = $id");
	// 	return $stmt->fetch();
	// }
	public function insertLivraison($date_livraison, $statut, $id_colis, $id_chauffeur, $id_vehicule, $id_entrepot,$adresse_destination ,$cout_vient) {
    $stmt = $this->db->prepare("INSERT INTO livraison (date_livraison, id_statut, id_colis, id_chauffeur,id_vehicule,id_entrepot,adresse_destination,cout_revient) VALUES (:daty, :statut, :colis, :chauffeur, :vehicule, :entrepot, :adresse_destination, :cout_revient)");
    return $stmt->execute([
        ':daty' => $date_livraison,
        ':statut' => $statut,
        ':colis' => $id_colis,
        ':chauffeur' => $id_chauffeur,
        ':vehicule' => $id_vehicule,
        ':entrepot' => $id_entrepot,
        ':adresse_destination' => $adresse_destination,
        ':cout_revient' => $cout_vient
    ]);
	}

    public function updateStatutLivraison($id, $statut) {
        $stmt = $this->db->prepare("UPDATE livraison SET id_statut = :statut WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':statut' => $statut
        ]);
    }
    public function beneficeByDay(){
        $stmt = $this->db->query("SELECT 
    l.date_livraison AS jour,

    SUM(c.poids_kg * p.gain_par_kg) AS chiffre_affaire,

    SUM(l.cout_revient 
        + ch.salaire_par_livraison 
        + v.cout_par_livraison) AS cout_total,

    SUM(c.poids_kg * p.gain_par_kg)
    - SUM(l.cout_revient 
          + ch.salaire_par_livraison 
          + v.cout_par_livraison) AS benefice

FROM livraison l
JOIN statut s ON l.id_statut = s.id
JOIN colis c ON l.id_colis = c.id
JOIN parametre p ON p.id = 1
JOIN chauffeur ch ON l.id_chauffeur = ch.id
JOIN vehicule v ON l.id_vehicule = v.id

WHERE s.nom = 'livré'
GROUP BY l.date_livraison
ORDER BY jour;
");
        return $stmt->fetchAll();
    }

    public function beneficeByMonth(){
        $stmt = $this->db->query("SELECT 
    EXTRACT(YEAR FROM l.date_livraison) AS annee,
    EXTRACT(MONTH FROM l.date_livraison) AS mois,

    SUM(c.poids_kg * p.gain_par_kg) AS chiffre_affaire,

    SUM(l.cout_revient 
        + ch.salaire_par_livraison 
        + v.cout_par_livraison) AS cout_total,

    SUM(c.poids_kg * p.gain_par_kg)
    - SUM(l.cout_revient 
          + ch.salaire_par_livraison 
          + v.cout_par_livraison) AS benefice

FROM livraison l
JOIN statut s ON l.id_statut = s.id
JOIN colis c ON l.id_colis = c.id
JOIN parametre p ON p.id = 1
JOIN chauffeur ch ON l.id_chauffeur = ch.id
JOIN vehicule v ON l.id_vehicule = v.id

WHERE s.nom = 'livré'
GROUP BY annee, mois
ORDER BY annee, mois;");
        return $stmt->fetchAll();
    }

    public function beneficeByYear(){
        $stmt = $this->db->query("SELECT 
    EXTRACT(YEAR FROM l.date_livraison) AS annee,

    SUM(c.poids_kg * p.gain_par_kg) AS chiffre_affaire,

    SUM(l.cout_revient 
        + ch.salaire_par_livraison 
        + v.cout_par_livraison) AS cout_total,

    SUM(c.poids_kg * p.gain_par_kg)
    - SUM(l.cout_revient 
          + ch.salaire_par_livraison 
          + v.cout_par_livraison) AS benefice

FROM livraison l
JOIN statut s ON l.id_statut = s.id
JOIN colis c ON l.id_colis = c.id
JOIN parametre p ON p.id = 1
JOIN chauffeur ch ON l.id_chauffeur = ch.id
JOIN vehicule v ON l.id_vehicule = v.id

WHERE s.nom = 'livré'
GROUP BY annee
ORDER BY annee;
");
        return $stmt->fetchAll();
    }

    public function getStatuts() {
        $stmt = $this->db->query("SELECT * FROM statut");
        return $stmt->fetchAll();
    }
    public function getColis() {
        $stmt = $this->db->query("SELECT * FROM colis");
        return $stmt->fetchAll();
    }
    public function getChauffeurs() {
        $stmt = $this->db->query("SELECT * FROM chauffeur");
        return $stmt->fetchAll();
    }
    public function getVehicules() {
        $stmt = $this->db->query("SELECT * FROM vehicule");
        return $stmt->fetchAll();
    }
    public function getEntrepots() {
        $stmt = $this->db->query("SELECT * FROM entrepot");
        return $stmt->fetchAll();
    }
	// public function updateProduit($id, $nom, $prix, $url_img) {
    // $stmt = $this->db->prepare("
    //     UPDATE PRODUITS 
    //     SET nom = :nom, url_img = :url_img, prix = :prix
    //     WHERE id = :id
    // ");

    // return $stmt->execute([
    //     ':id'      => $id,
    //     ':nom'     => $nom,
    //     ':url_img' => $url_img,
    //     ':prix'    => $prix
    // ]);
	// }

	 public function annulerLivraison($id) {
     $stmt = $this->db->prepare("
         UPDATE livraison
         SET id_statut = (SELECT id FROM statut WHERE nom = 'annulé')
         WHERE id = :id;
     ");
        return $stmt->execute([
            ':id'      => $id
        ]);
    }
    public function validateLivraison($id) {
        $stmt = $this->db->prepare("
            UPDATE livraison
            SET id_statut = (SELECT id FROM statut WHERE nom = 'livré')
            WHERE id = :id;
        ");
        return $stmt->execute([
            ':id'      => $id
        ]);
    }

    // return $stmt->execute([
    //     ':id'      => $id
    // ]);
	// }
}
?>