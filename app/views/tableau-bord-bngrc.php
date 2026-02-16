<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Tableau de Bord</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-card .subtext {
            color: #999;
            font-size: 0.85em;
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filters input, .filters select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .filters input:focus, .filters select:focus {
            border-color: #667eea;
        }

        .filters input {
            flex: 1;
            min-width: 200px;
        }

        .cities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .city-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .city-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .city-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .city-name {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-critique {
            background: #fee;
            color: #d32f2f;
        }

        .status-moyen {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-bon {
            background: #e8f5e9;
            color: #388e3c;
        }

        .needs-section {
            margin-bottom: 20px;
        }

        .need-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .need-icon {
            font-size: 1.5em;
            margin-right: 12px;
        }

        .need-details {
            flex: 1;
        }

        .need-label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 3px;
        }

        .need-value {
            font-weight: bold;
            color: #333;
        }

        .progress-section {
            margin-bottom: 20px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9em;
            color: #666;
        }

        .progress-bar {
            height: 12px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            transition: width 0.3s ease;
        }

        .progress-fill.low {
            background: linear-gradient(90deg, #f44336 0%, #d32f2f 100%);
        }

        .progress-fill.medium {
            background: linear-gradient(90deg, #ff9800 0%, #f57c00 100%);
        }

        .progress-fill.high {
            background: linear-gradient(90deg, #4caf50 0%, #388e3c 100%);
        }

        .btn-details {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .btn-details:hover {
            opacity: 0.9;
        }

        .sidebar {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .sidebar h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3em;
        }

        .recent-donation {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }

        .donation-amount {
            font-weight: bold;
            color: #667eea;
            font-size: 1.1em;
        }

        .donation-time {
            font-size: 0.85em;
            color: #999;
            margin-top: 3px;
        }

        @media (max-width: 768px) {
            .cities-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filters {
                flex-direction: column;
            }

            .filters input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>🏛️ BNGRC - Suivi des Dons aux Sinistrés</h1>
            <p>Tableau de bord de gestion et distribution des dons</p>
        </div>

        <!-- Statistiques globales -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Besoins Totaux</h3>
                <div class="value">45 250 000 Ar</div>
                <div class="subtext">Tous les besoins recensés</div>
            </div>
            <div class="stat-card">
                <h3>Dons Reçus</h3>
                <div class="value">28 500 000 Ar</div>
                <div class="subtext">Montant total collecté</div>
            </div>
            <div class="stat-card">
                <h3>Taux de Couverture</h3>
                <div class="value">63%</div>
                <div class="subtext">Des besoins sont couverts</div>
            </div>
            <div class="stat-card">
                <h3>Villes Assistées</h3>
                <div class="value">8 / 12</div>
                <div class="subtext">Villes avec dons attribués</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <input type="text" placeholder="🔍 Rechercher une ville..." id="searchCity">
            <select id="filterStatus">
                <option value="">Tous les statuts</option>
                <option value="critique">Situation critique (&lt;30%)</option>
                <option value="moyen">Situation moyenne (30-70%)</option>
                <option value="bon">Situation bonne (&gt;70%)</option>
            </select>
            <select id="filterCategory">
                <option value="">Toutes les catégories</option>
                <option value="nature">Besoins en nature</option>
                <option value="materiau">Besoins en matériaux</option>
                <option value="argent">Besoins en argent</option>
            </select>
        </div>

        <!-- Derniers dons -->
        <div class="sidebar">
            <h2>📋 Derniers Dons Reçus</h2>
            <div class="recent-donation">
                <div class="donation-amount">2 500 000 Ar</div>
                <div class="donation-time">Il y a 15 minutes - Don en argent</div>
            </div>
            <div class="recent-donation">
                <div class="donation-amount">500 kg de riz</div>
                <div class="donation-time">Il y a 1 heure - Don en nature</div>
            </div>
            <div class="recent-donation">
                <div class="donation-amount">200 tôles</div>
                <div class="donation-time">Il y a 2 heures - Don en matériaux</div>
            </div>
        </div>

        <!-- Grille des villes -->
        <div class="cities-grid">
            <!-- Ville 1 - Antananarivo -->
            <div class="city-card" data-status="moyen">
                <div class="city-header">
                    <div class="city-name">Antananarivo</div>
                    <span class="status-badge status-moyen">Moyen 55%</span>
                </div>
                
                <div class="needs-section">
                    <div class="need-item">
                        <div class="need-icon">🍚</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en nature</div>
                            <div class="need-value">8 500 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">🏠</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en matériaux</div>
                            <div class="need-value">12 000 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">💰</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en argent</div>
                            <div class="need-value">3 500 000 Ar</div>
                        </div>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Dons attribués</span>
                        <strong>13 200 000 Ar / 24 000 000 Ar</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill medium" style="width: 55%"></div>
                    </div>
                </div>

                <button class="btn-details">Voir les détails</button>
            </div>

            <!-- Ville 2 - Toamasina -->
            <div class="city-card" data-status="critique">
                <div class="city-header">
                    <div class="city-name">Toamasina</div>
                    <span class="status-badge status-critique">Critique 22%</span>
                </div>
                
                <div class="needs-section">
                    <div class="need-item">
                        <div class="need-icon">🍚</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en nature</div>
                            <div class="need-value">5 200 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">🏠</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en matériaux</div>
                            <div class="need-value">8 800 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">💰</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en argent</div>
                            <div class="need-value">2 000 000 Ar</div>
                        </div>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Dons attribués</span>
                        <strong>3 520 000 Ar / 16 000 000 Ar</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill low" style="width: 22%"></div>
                    </div>
                </div>

                <button class="btn-details">Voir les détails</button>
            </div>

            <!-- Ville 3 - Antsirabe -->
            <div class="city-card" data-status="bon">
                <div class="city-header">
                    <div class="city-name">Antsirabe</div>
                    <span class="status-badge status-bon">Bon 82%</span>
                </div>
                
                <div class="needs-section">
                    <div class="need-item">
                        <div class="need-icon">🍚</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en nature</div>
                            <div class="need-value">3 500 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">🏠</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en matériaux</div>
                            <div class="need-value">2 000 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">💰</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en argent</div>
                            <div class="need-value">1 500 000 Ar</div>
                        </div>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Dons attribués</span>
                        <strong>5 740 000 Ar / 7 000 000 Ar</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill high" style="width: 82%"></div>
                    </div>
                </div>

                <button class="btn-details">Voir les détails</button>
            </div>

            <!-- Ville 4 - Fianarantsoa -->
            <div class="city-card" data-status="moyen">
                <div class="city-header">
                    <div class="city-name">Fianarantsoa</div>
                    <span class="status-badge status-moyen">Moyen 48%</span>
                </div>
                
                <div class="needs-section">
                    <div class="need-item">
                        <div class="need-icon">🍚</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en nature</div>
                            <div class="need-value">4 200 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">🏠</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en matériaux</div>
                            <div class="need-value">3 800 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">💰</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en argent</div>
                            <div class="need-value">2 000 000 Ar</div>
                        </div>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Dons attribués</span>
                        <strong>4 800 000 Ar / 10 000 000 Ar</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill medium" style="width: 48%"></div>
                    </div>
                </div>

                <button class="btn-details">Voir les détails</button>
            </div>

            <!-- Ville 5 - Mahajanga -->
            <div class="city-card" data-status="critique">
                <div class="city-header">
                    <div class="city-name">Mahajanga</div>
                    <span class="status-badge status-critique">Critique 15%</span>
                </div>
                
                <div class="needs-section">
                    <div class="need-item">
                        <div class="need-icon">🍚</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en nature</div>
                            <div class="need-value">6 000 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">🏠</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en matériaux</div>
                            <div class="need-value">5 500 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">💰</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en argent</div>
                            <div class="need-value">1 500 000 Ar</div>
                        </div>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Dons attribués</span>
                        <strong>1 950 000 Ar / 13 000 000 Ar</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill low" style="width: 15%"></div>
                    </div>
                </div>

                <button class="btn-details">Voir les détails</button>
            </div>

            <!-- Ville 6 - Toliara -->
            <div class="city-card" data-status="bon">
                <div class="city-header">
                    <div class="city-name">Toliara</div>
                    <span class="status-badge status-bon">Bon 75%</span>
                </div>
                
                <div class="needs-section">
                    <div class="need-item">
                        <div class="need-icon">🍚</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en nature</div>
                            <div class="need-value">2 800 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">🏠</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en matériaux</div>
                            <div class="need-value">3 200 000 Ar</div>
                        </div>
                    </div>
                    <div class="need-item">
                        <div class="need-icon">💰</div>
                        <div class="need-details">
                            <div class="need-label">Besoins en argent</div>
                            <div class="need-value">2 000 000 Ar</div>
                        </div>
                    </div>
                </div>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Dons attribués</span>
                        <strong>6 000 000 Ar / 8 000 000 Ar</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill high" style="width: 75%"></div>
                    </div>
                </div>

                <button class="btn-details">Voir les détails</button>
            </div>
        </div>
    </div>

    <script>
        // Fonctionnalité de recherche
        document.getElementById('searchCity').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cityCards = document.querySelectorAll('.city-card');
            
            cityCards.forEach(card => {
                const cityName = card.querySelector('.city-name').textContent.toLowerCase();
                if (cityName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Filtre par statut
        document.getElementById('filterStatus').addEventListener('change', function(e) {
            const status = e.target.value;
            const cityCards = document.querySelectorAll('.city-card');
            
            cityCards.forEach(card => {
                if (status === '' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Gestion des boutons détails
        document.querySelectorAll('.btn-details').forEach(button => {
            button.addEventListener('click', function() {
                const cityName = this.closest('.city-card').querySelector('.city-name').textContent;
                alert('Détails pour ' + cityName + '\n\nCette fonctionnalité ouvrira une page avec:\n- Liste détaillée des besoins\n- Historique des dons reçus\n- Distribution par catégorie\n- Statistiques avancées');
            });
        });
    </script>
</body>
</html>
