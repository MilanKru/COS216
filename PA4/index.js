//Milan Kruger
//u04948123

const api = localStorage.getItem('apiKey');
if(!api)
{
    window.location.href = 'login.php';
}
const url = 'https://wheatley.cs.up.ac.za/u04948123/api.php';

setTheme();
getPreference();
function logout()
{
    setPreference();
    localStorage.setItem('apiKey',""); 
}

function setPreference()
{
    let xml = new XMLHttpRequest();
    xml.open('POST', url, true);
    xml.setRequestHeader('Content-Type', 'application/json');

    const username = 'u04948123';
    const password = '3918Milo';
    const auth = 'Basic ' + btoa(username + ':' + password);
    xml.setRequestHeader('Authorization', auth);
    requestDataListings.type = 'SetPreferences';
    xml.send(JSON.stringify(requestDataListings));
}
const requestDataListings = {
    apikey: api,
    type: 'GetAllListings',
    limit: 30,
    sort: 'id',
    order: 'ASC',
    return: '*',
    search: {
        title: '',
        location: '',
        price_min: '0',
        price_max: '100000000',
        bedrooms: '2',
        bathrooms: '2',
        parking_spaces: '2',
        amenities: '',
        type: 'sale',
    },
    search_on: 1,
};

async function fetchListings(type="GetAllListings",adding=false) 
{
    const oldsearch = requestDataListings.search;
    requestDataListings.type = type;
    if(type === "GetFavourites" && adding == true)
    {
        requestDataListings.search = {};
    }
    return new Promise((resolve) => 
    {
        let xml = new XMLHttpRequest();
        xml.open('POST', url, true);
        xml.setRequestHeader('Content-Type', 'application/json');

        const username = 'u04948123';
        const password = '3918Milo';
        const auth = 'Basic ' + btoa(username + ':' + password);
        xml.setRequestHeader('Authorization', auth);

        xml.onreadystatechange = function () 
        {
            if (xml.readyState === 4 && xml.status === 200) 
            {
                let response = JSON.parse(xml.responseText);
                resolve(response);
            }
        };
        xml.send(JSON.stringify(requestDataListings));
        requestDataListings.search = oldsearch;
    });
}

async function load_listings(type="GetAllListings",search) 
{
    setPreference();
    if(!api)
    {
        window.location.href = 'login.php';
    }
    requestDataListings.type = type;
    document.getElementById('loading-container').style.display = 'block';

    let listings = await fetchListings(type);
    listings = listings.data;
    var list = document.getElementById('house-list');
    var elements = list.querySelectorAll('li');
    elements.forEach(function(li) {
        li.parentNode.removeChild(li);
    });
    var existingMessage = list.querySelector('.empty-message');
    if (existingMessage) {
        list.removeChild(existingMessage);
    }

    let favourites = await fetchListings('GetFavourites',true);
    favourites = favourites.data;
    if(listings.length === 0) {
        var message = document.createElement('div');
        message.classList.add('empty-message');
        message.style.color = '#EDF5E1';
        message.style.background = '#05386B';
        message.style.fontSize = '50px';
        message.style.marginLeft = '5vw';
        message.style.padding = '1vw';
        message.style.borderRadius = '5px';
        message.style.width = '30vw';
        message.textContent = 'It appears this search is empty, try other options :D';
        list.appendChild(message);
    }
    
    for (const item of listings) {
        var listItem = document.createElement('li');
        

        let images = item.images.split(",");
        var anchor = document.createElement('a');
        anchor.href = 'view.php?id=' + item.id;
        anchor.classList.add('view-button');
    
        var image = document.createElement('img');
        image.classList.add('house-thumbnail');
        image.src = images[0];
        image.alt = "House";
    
        anchor.appendChild(image);

        var houseInteractButtonsDiv = document.createElement('div');
        houseInteractButtonsDiv.id = item.id;
        houseInteractButtonsDiv.classList.add('house-interact-buttons');
        houseInteractButtonsDiv.id = "interactButton-"+item.id;
        var heartButton = document.createElement('button');
        heartButton.classList.add('fav-button');
        var heartIcon = document.createElement('i');

        var idFound = false;
        for (var i = 0; i < favourites.length; i++) 
        {
            if (favourites[i].id === item.id) 
            {
                idFound = true;
                break;
            }
        }
        
        if(idFound)
        {
            heartButton.style.color = 'RED';
        }
        else
        {
            heartButton.style.color = 'WHITE';
        }
        heartIcon.classList.add('fa', 'fa-heart');
        heartButton.appendChild(heartIcon);


        
        var contactButton = document.createElement('button');
        contactButton.classList.add('contact-button');
        var phoneIcon = document.createElement('i');
        phoneIcon.classList.add('fa', 'fa-phone');
        contactButton.appendChild(phoneIcon);

        contactButton.addEventListener('click', function() {
            window.location.href = item.url;
        });

        heartButton.addEventListener('click', async function() 
        {  
            var idFound = false;
            for (var i = 0; i < favourites.length; i++) 
            {
                if (favourites[i].id === item.id) 
                {
                    idFound = true;
                    break;
                }
            }
            if(!idFound)
            {
                await modify_favourites('AddFavourites',item.id)
            }
            else
            {
                await modify_favourites('RemoveFavourites',item.id)
            }
        
            await load_listings(type);
        });


        houseInteractButtonsDiv.appendChild(heartButton);
        houseInteractButtonsDiv.appendChild(contactButton);
        var Title = document.createElement('h1');
        var Price = document.createElement('div');
        var Location = document.createElement('div');
        var Bedrooms = document.createElement('div');
        var Bathrooms = document.createElement('div');
    
        Title.textContent = item.title;
        if(requestDataListings.search.type === 'sale') {
            Price.textContent = "Price: R" + item.price;
        } else {
            Price.textContent = "Price: R" + item.price + " per month";
        }
        Location.textContent = "Location: " + item.location;
        Bedrooms.textContent = "Bedrooms: " + item.bedrooms;
        Bathrooms.textContent = "Bathrooms " + item.bathrooms;
    
        listItem.appendChild(anchor);
        listItem.appendChild(Title);
        listItem.appendChild(Price);
        listItem.appendChild(Location);
        listItem.appendChild(Bedrooms);
        listItem.appendChild(Bathrooms);
        listItem.appendChild(houseInteractButtonsDiv);
        list.appendChild(listItem);
    }
    document.getElementById('loading-container').style.display = 'none';
}
async function load_listings_title(type="GetAllListings")
{
    requestDataListings.sort = 'title';
    load_listings(type);
}
async function load_listings_loc(type="GetAllListings")
{
    requestDataListings.sort = 'location';
    load_listings(type);
}
async function load_listings_price(type="GetAllListings")
{
    requestDataListings.sort = 'price';
    load_listings(type);
}
async function load_listings_bed(type="GetAllListings")
{
    requestDataListings.sort = 'bedrooms';
    load_listings(type);
}
async function load_listings_bath(type="GetAllListings")
{
    requestDataListings.sort = 'bathrooms';
    load_listings(type);
}
async function load_listings_park(type="GetAllListings")
{
    requestDataListings.sort = 'parking_spaces';
    load_listings(type);
}
async function load_listings_buy(type="GetAllListings")
{
    requestDataListings.search.type = 'sale';
    load_listings(type);
}
async function load_listings_rent(type="GetAllListings")
{
    requestDataListings.search.type = 'rent';
    load_listings(type);
}
async function load_listings_bedrooms(type="GetAllListings")
{
    const bed = document.getElementById('bedrooms').value;
    requestDataListings.search.bedrooms = bed;
    load_listings(type);
}
async function load_listings_bathrooms(type="GetAllListings")
{
    const bath = document.getElementById('bathrooms').value;
    requestDataListings.search.bathrooms = bath;
    load_listings(type);
}
async function load_listings_max(type="GetAllListings")
{
    const max = document.getElementById('price-max').value;
    requestDataListings.search.price_max = max;
    load_listings(type);
}
async function load_listings_min(type="GetAllListings")
{
    const min = document.getElementById('price-min').value;
    requestDataListings.search.price_min = min;
    load_listings(type);
}
async function load_listings_search(type="GetAllListings")
{
    const searchText = document.getElementById('search-input');
    const searchValue = searchText.value;
    requestDataListings.search.title = searchValue;
    load_listings(type);
}
async function load_listings_parkingspaces(type="GetAllListings")
{
    const spaces = parseInt(document.getElementById('parkingspaces').value);
    requestDataListings.search.parking_spaces = spaces ;
    load_listings(type);
}
async function load_listings_limit(type="GetAllListings")
{
    let limittext = parseInt(document.getElementById('limits').value);
    if(limittext >= 500)
    {
        limittext = 500;
    }
    if(limittext <= 1)
    {
        limittext = 1;
    }
    requestDataListings.limit = limittext;
    
    load_listings(type);
}
async function load_listings_asc_desc(type="GetAllListings")
{
    const icon = document.getElementById('asc-desc-icon');
    if(icon.classList.contains("fa-angle-down"))
    {
        icon.classList.remove("fa-angle-down");
        icon.classList.add("fa-angle-up");
        requestDataListings.order = "DESC";
    }
    else {
        icon.classList.remove("fa-angle-up");
        icon.classList.add("fa-angle-down");
        requestDataListings.order = "ASC";
    }
    load_listings(type);
}
async function load_listings_reset_filters(type="GetAllListings")
{
    let newsearch = {
        type: requestDataListings.search.type,
    }
    requestDataListings.search = newsearch;
    load_listings(type);
}

const requestDataAgents = {
    type: 'GetAllAgents',
    limit: 10,
};
async function fetchAgents() 
{
    return new Promise((resolve) => 
    {
        let xml = new XMLHttpRequest();
        xml.open('POST', url, true);
        xml.setRequestHeader('Content-Type', 'application/json');

        const username = 'u04948123';
        const password = '3918Milo';
        const auth = 'Basic ' + btoa(username + ':' + password);
        xml.setRequestHeader('Authorization', auth);

        xml.onreadystatechange = function () {
            if (xml.readyState === 4 && xml.status === 200) 
            {
                let response = JSON.parse(xml.responseText);
                resolve(response);
            }
        };
        xml.send(JSON.stringify(requestDataAgents));
    });
}
async function load_agents(l)
{
    document.getElementById('loading-container').style.display = 'block';
    let agents = await fetchAgents();
    var list = document.getElementById('agent-list');
    var elements = list.querySelectorAll('li');
    elements.forEach(function(li) {
        li.parentNode.removeChild(li);
    });
    var existingMessage = list.querySelector('.empty-message');
    if (existingMessage) {
        list.removeChild(existingMessage);
    }
    agents.data.forEach(async function(item) {
        var listItem = document.createElement('li');
        
        let images = item.logo;
        var anchor = document.createElement('a');
        anchor.href = item.url;
        anchor.classList.add('view-button');

        var image = document.createElement('img');
        image.classList.add('house-thumbnail');
        image.src = images;
        image.alt = "House";

        anchor.appendChild(image);
        
        var Name = document.createElement('h1');
        var Description = document.createElement('div');


        Name.textContent = item.name;
        Description.textContent = "Description: "+item.description;


        listItem.appendChild(anchor);
        listItem.appendChild(Name);
        listItem.appendChild(Description);
        list.appendChild(listItem);
      });
      document.getElementById('loading-container').style.display = 'none';
}
async function load_agents_limit()
{
    const limittext = parseInt(document.getElementById('limits').value);
    requestDataAgents.limit = limittext;
    load_agents();
}

async function load_view()
{
    document.getElementById('loading-container').style.display = 'block';
    var urlParams = new URLSearchParams(window.location.search);

    var itemId = urlParams.get('id');
    requestDataListings.search = {
        id: itemId,
    }

    let item = await fetchListings();
    item = item.data;
    itemImages = item[0].images.split(",");
    const carousel = document.querySelector('.carousel');
    const slides = document.querySelector('.slides');

    function createImage(src) {
        const img = document.createElement('img');
        img.src = src;
        img.alt = 'Image';
        return img;
    }

    if (itemImages.length > 0) 
    {
        itemImages.forEach(imageUrl => {
            const img = createImage(imageUrl);
            slides.appendChild(img);
        });

        const images = document.querySelectorAll('.slides img');

        const prevBtn = document.createElement('button');
        prevBtn.textContent = '<';
        const nextBtn = document.createElement('button');
        prevBtn.classList.add("prev");
        nextBtn.textContent = '>';
        nextBtn.classList.add("next");
        carousel.appendChild(prevBtn);
        carousel.appendChild(nextBtn);

        let currentIndex = 0;

        let length = images.length-2;
        function showSlide(index) {
            images.forEach(image => {
                image.style.display = 'none';
            });
            images[index].style.display = 'block';
        }

        function prevSlide() 
        {
            currentIndex--;
            if(currentIndex < 0)
            {
                currentIndex = length;
            }
            showSlide(currentIndex);
        }

        function nextSlide() 
        {
            currentIndex++;
            if (currentIndex > length) {

                currentIndex = 0;
            }
            showSlide(currentIndex);
        }
        
        showSlide(currentIndex);

        prevBtn.addEventListener('click', prevSlide);
        nextBtn.addEventListener('click', nextSlide);
    } else 
    {
        console.error('No images found for the item.');
    }

    const header = document.querySelector(".listing-details h1");
    header.textContent = item[0].title;


    const info = document.querySelector(".listing-details .listing-info");

    const description = document.createElement('div');
    let text = item[0].description.replace(/,/g, '');
    description.textContent = text;
    description.style.fontSize = '30px';


    const price = document.createElement('div');
    price.textContent = "Price: R"+item[0].price;

    const location = document.createElement('div');
    location.textContent = "Location: " + item[0].location;

    const bedrooms = document.createElement('div');
    bedrooms.textContent = "Bedrooms: "+ item[0].bedrooms;

    const bathrooms = document.createElement('div');
    bathrooms.textContent = "Bedrooms: "+ item[0].bathrooms;


    const amenities = document.createElement('div');
    text = item[0].amenities.replace(/,/g, '');
    if(text.length === 0)
    {
        text = "None";
    }
    amenities.textContent = "Amenities: " + text;
    amenities.style.fontSize = '40px';



    const call = document.createElement('button');
    const callbutton = document.createElement('i');

    callbutton.classList.add('call-button');

        
    callbutton.classList.add("fa", "fa-phone"); 

    call.style.fontSize = '50px';
    call.style.background = 'transparent';
    call.style.border = 'none';

    call.addEventListener('click', function() {

        window.location.href = item[0].url;
    });

    
    call.appendChild(callbutton);
    info.appendChild(call);
    info.appendChild(price);
    info.appendChild(location);
    info.appendChild(bedrooms);
    info.appendChild(bathrooms);
    info.appendChild(amenities);
    info.appendChild(description);

    


    document.getElementById('loading-container').style.display = 'none';
    
}

async function modify_favourites(type,id)
{
    return new Promise((resolve) => 
        {
            let xml = new XMLHttpRequest();
            xml.open('POST', url, true);
            xml.setRequestHeader('Content-Type', 'application/json');
            
            const username = 'u04948123';
            const password = '3918Milo';
            const auth = 'Basic ' + btoa(username + ':' + password);
            
            xml.setRequestHeader('Authorization', auth);
            xml.onreadystatechange = function () 
            {
                if (xml.readyState === 4 && xml.status === 200) 
                {
                    let response = JSON.parse(xml.responseText);
                    resolve(response);
                }
            };
            xml.send(JSON.stringify({
                type: type,
                apikey: api,
                listings_id: id
            }));
        });
}

async function setTheme(enabled=false)
{
    var element = document.body;
    let theme = await getTheme();
    theme = theme.data[0].theme;
    if(theme === 'light')
    {
        element.classList.toggle("light-mode");
    }
    if(theme === 'dark')
    {
        element.classList.toggle("dark-mode");
    }
    if(enabled === true)
    {
        if (theme === 'light') 
        {
            theme = 'dark';
            element.classList.toggle("dark-mode");
        } 
        else if (theme === 'dark') 
        {
            theme = 'light';
            element.classList.toggle("light-mode");
        }

        const xml = new XMLHttpRequest();
        xml.open('POST', url, true);
        xml.setRequestHeader('Content-Type', 'application/json');

        const username = 'u04948123';
        const password = '3918Milo';
        const auth = 'Basic ' + btoa(username + ':' + password);
        xml.setRequestHeader('Authorization', auth);

        xml.send(JSON.stringify({
            type: "SetTheme",
            apikey: api,
            theme: theme
        }));
    }
}

async function getTheme()
{
    {
        return new Promise((resolve) => 
        {
            let xml = new XMLHttpRequest();
            xml.open('POST', url, true);
            xml.setRequestHeader('Content-Type', 'application/json');
            
            const username = 'u04948123';
            const password = '3918Milo';
            const auth = 'Basic ' + btoa(username + ':' + password);
            xml.setRequestHeader('Authorization', auth);

            xml.onreadystatechange = function () {
                if (xml.readyState === 4 && xml.status === 200) 
                {
                    let response = JSON.parse(xml.responseText);
                    resolve(response);
                }
            };
            xml.send(JSON.stringify({
                type: "GetTheme",
                apikey: api,
            }));
        });
    }
}

async function fetchPreference()
{
    return new Promise((resolve) => 
        {
            let xml = new XMLHttpRequest();
            xml.open('POST', url, true);
            xml.setRequestHeader('Content-Type', 'application/json');
            
            const username = 'u04948123';
            const password = '3918Milo';
            const auth = 'Basic ' + btoa(username + ':' + password);
            
            xml.setRequestHeader('Authorization', auth);
            xml.onreadystatechange = function () 
            {
                if (xml.readyState === 4 && xml.status === 200) 
                {
                    let response = JSON.parse(xml.responseText);
                    resolve(response);
                }
            };
            xml.send(JSON.stringify({
                type: 'GetPreferences',
                apikey: api,
            }));
        });
}

async function getPreference()
{
    let preferences = await fetchPreference();
    preferences = preferences.data;
    requestDataListings.limit = preferences.limit;
    requestDataListings.order = preferences.order;
    requestDataListings.return = preferences.return;
    const array = preferences.search.slice(1, -1).split(",").map(item => 
        {
        if (item === "") return "";
        if (!isNaN(item)) return parseFloat(item);
        return item;
    });
    requestDataListings.search.title = array[0];
    requestDataListings.search.location = array[1];
    requestDataListings.search.price_min = array[2];
    requestDataListings.search.price_max = array[3];
    requestDataListings.search.bedrooms = array[4];
    requestDataListings.search.bathrooms = array[5];
    requestDataListings.search.parking_spaces = array[6];
    requestDataListings.search.amenities = array[7];
    requestDataListings.search.type = array[8];
}