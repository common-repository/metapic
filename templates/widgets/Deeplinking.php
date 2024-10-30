<div class="metapic-dashboard-container">
    <div class="metapic-dashboard-logo"></div>
    <p id="metapic-dashboard-heading"><?= __("Here you can create your Metapic link!", 'metapic') ?></p>
    <p><?= __("Paste a link to a product or store, click create and receive your tracked link.", 'metapic') ?></p>
	<div class="metapic-input-container">
        <input type="text" autocomplete="off" placeholder="Paste Your Link.." id="metapic-deeplink-url" class="metapic-link-input">
        <button id="metapic-deeplink-button" class="metapic-btn metapic-btn-create-link"> Create </button>
        <button id="metapic-deeplink-copy" class="metapic-btn metapic-btn-copy-link">Copy</button>
    </div>
    <div id="metapic-cost-container" class="metapic-cost-container">
        <div id="revenue-cpc"></div>
        <div id="revenue-instagram-cpc"></div>
        <div id="revenue-cpa"></div>
        <div id="revenue-instagram-cpa"></div>
    </div>
    <span id="metapic-posting-text" style="display: none">Read our <a href="https://app.metapic.com/#/help" target="_blank" rel="noopener noreferrer">posting guidelines</a> before publishing your links.</span>
    <button id="metapic-deeplink-reset" class="metapic-btn metapic-btn-reset">Create new link</button>
    <p><?= __("To find the stores that supports metapic deep linking, and what revenue they offer, click the button below.", 'metapic') ?></p>
    <button id="metapic-show-all-stores" class="metapic-btn metapic-btn-go-to-stores">Go to stores</button>
</div>
<script type="text/javascript">
    (function($) {
        $("#metapic-show-all-stores").on("click", function(e) {
            e.preventDefault();
            $.event.trigger({
                type: "metapic",
                baseUrl: "//app.metapic.com",
                startPage: "newStores",
                hideSidebar: true,
                randomKey: "<?= get_option("mtpc_access_token") ?>"
            });
        });
        $("#metapic-deeplink-button").on("click", function(e) {
            e.preventDefault();
            document.querySelector("#metapic-deeplink-button").disabled = true;
            let deeplinkUrl=document.getElementById("metapic-deeplink-url").value;
            createDeepLink(2, "<?= get_option("mtpc_access_token") ?>",  deeplinkUrl);
        });
        $("#metapic-deeplink-copy").on("click", function(e) {
            e.preventDefault();
            document.querySelector("#metapic-deeplink-url").select();
            document.execCommand("copy");
            document.querySelector("#metapic-deeplink-copy").textContent = "Copied!";
        });
        $("#metapic-deeplink-reset").on("click", function(e) {
            e.preventDefault();
            document.querySelector("#metapic-deeplink-url").value = "";
            document.querySelector("#metapic-deeplink-button").style.display = "block";
            document.querySelector("#metapic-deeplink-button").disabled = false;
            document.querySelector("#metapic-deeplink-copy").style.display = "none";
            document.querySelector("#metapic-deeplink-reset").style.display = "none";
            document.querySelector("#metapic-deeplink-copy").textContent = "Copy";
            document.querySelector("#revenue-cpc").textContent = "";
            document.querySelector("#revenue-instagram-cpc").textContent = "";
            document.querySelector("#revenue-cpa").textContent = "";
            document.querySelector("#revenue-instagram-cpa").textContent = "";
            document.querySelector("#revenue-cpc").style.display = "none";
            document.querySelector("#revenue-instagram-cpc").style.display = "none";
            document.querySelector("#revenue-cpa").style.display = "none";
            document.querySelector("#revenue-instagram-cpa").style.display = "none";
            document.querySelector("#metapic-posting-text").style.display = "none";
        });
        async function createDeepLink($userId, $accessToken, $url) {
            let apiUrl="https://api.metapic.com"
            const url = apiUrl + '/deepLinkBlogPost/' + $userId;
            const data = {
                'blogPost': '<a href="' + $url + '">' + $url + '</a>'
            };
            const params = {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': $accessToken
                }
            }

            fetch(url, params)
                .then((response) => {
                    if (!response.ok) {
                        throw Error('Can not create deeplink');
                    }
                    return response.json();
                    })
                .then(data => {
                    if (data.isUpdated === true) {
                        const info = data.linkTranslation[0];
                        const link = info.after;
                        document.querySelector("#metapic-deeplink-url").value = link;
                        document.querySelector("#metapic-deeplink-button").style.display = "none";
                        document.querySelector("#metapic-deeplink-copy").style.display = "block";
                        document.querySelector("#metapic-deeplink-reset").style.display = "block";
                        document.querySelector("#metapic-posting-text").style.display = "block";

                        if(info["user_revenue_cpc"] > 0) {
                            const element = document.querySelector("#revenue-cpc");
                            element.style.display = "block";
                            element.textContent = "Blog & YouTube " + info["user_revenue_cpc_formated"] + " per click";
                        }
                        if(info["user_instagram_cpc"] > 0) {
                            const element = document.querySelector("#revenue-instagram-cpc");
                            element.style.display = "block";
                            element.textContent = "Instagram " + info["user_instagram_cpc_formated"] + " per click";
                        }
                        if(info["user_revenue_cpa"] > 0) {
                            const element = document.querySelector("#revenue-cpa");
                            element.style.display = "block";
                            element.textContent = "Blog & YouTube " + info["user_revenue_cpa"]*100 + "% per sale";
                        }
                        if(info["user_instagram_cpa"] > 0) {
                            const element = document.querySelector("#revenue-instagram-cpa");
                            element.style.display = "block";
                            element.textContent = "Instagram " + info["user_instagram_cpa"]*100 + "% per sale";
                        }
                    } else {
                        throw Error('Can not create deeplink for this store');
                    }
                })
                .catch(error => {
                    console.log("error",error);
                });
        }
        function toggleDeepLinkLoader($loader){

        }
    })(jQuery);
</script>
