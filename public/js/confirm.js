// Confirmation de suppression
document.addEventListener('DOMContentLoaded', function() {
    const deleteLinks = document.querySelectorAll('a[href*="/suppr/"]');

    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const productName = this.closest('.product-card').querySelector('h2').textContent;
            if (!confirm('Voulez-vous vraiment supprimer ce produit ?')) {
                e.preventDefault();
            }
        });
    });
});