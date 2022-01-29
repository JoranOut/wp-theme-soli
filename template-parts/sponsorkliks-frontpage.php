<?php
?>
<script>
  window.addEventListener('load', function(){
    const loadShops = async () => {
      const response = await fetch('https://api.sponsorkliks.com/v1.0/?call=webshops_club&club=3969&show=json');
      const shops = await response.json();

      let k = 12;
      let n = shops.webshops.length;
      for (let i = 0; i < k; i++) {
        let j = ~~(i + Math.random() * Math.random() * Math.random() * (n - i)) || 0;
        let x = shops.webshops[i];
        shops.webshops[i] = shops.webshops[j];
        shops.webshops[j] = x;
      }
      shops.webshops.length = k;

      return shops.webshops;
    };
    loadShops().then((shops)=>{
      const shop_container = document.querySelector('.sponsorkliks .shops');
      shops.forEach(shop => {
        let shop_div = document.createElement('a');
        let img = document.createElement('img');
        shop_div.classList.add("shop");
        shop_div.href = shop.link;
        shop_div.target = "_blank";
        shop_div.title = shop.name_short + " ("+shop.commission_gross+")";
        img.src = shop.logo_120x60;

        shop_container.appendChild(shop_div);
        shop_div.appendChild(img);
      });
    });
  });
</script>

<article class="sponsorkliks">
  <div class="container">
    <h1>Steun Soli</h1>
    <p>Het sponsoren van Soli kan tegenwoordig ook gratis! Sponsorkliks zorgt ervoor dat een commissie van je bestelling bij webshops naar Soli gaat. Het kost niks, maar levert de club veel op! Ga naar <a href="https://www.sponsorkliks.com/products/shops.php?club=3969&cn=nl&ln=nl" target="_blank">sponsorkliks.nl</a> voor meer winkels en informatie, <a href="https://chrome.google.com/webstore/detail/sponsorkliks/ibddmcijlhljfdinapegnidiodopoadi?hl=nl" target="_blank">de nieuwe browserextentie</a> of probeer het direct uit via een van de onderstaande links.</p>
    <div class="shops">

    </div>
    <p>Steun je Soli liever direct? Wordt <a href="https://www.soli.nl/vereniging/overig/vrienden-van-soli/" target="_blank">vriend van Soli</a> of kijk op <a href="https://winkel.soli.nl" target="_blank">onze webshop</a> naar de nieuwste mogelijkheden.</p>
  </div>
</article>
