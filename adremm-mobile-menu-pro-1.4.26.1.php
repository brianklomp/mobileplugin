<?php
/**
 * Plugin Name: ADREMM Mobile Menu PRO
 * Description: Mobiele drawer/fullscreen navigatie met drilldown, per-laag typografie, socials/credits en RGBA colors. Dynamische Google Fonts via API.
 * Plugin URI: https://adremm.nl/developments
 * Version: 1.4.26.1
 * Author: ADREMM
 * Author URI: https://adremm.nl
 * License: GPLv2 or later
 * Text Domain: adremm-mobile-menu-pro
 */

if ( ! defined('ABSPATH') ) exit;

define('ADREMM_MMP_VERSION', '1.4.26.1');
define('ADREMM_MMP_SLUG','adremm-mm-pro');
define('ADREMM_MMP_OPT','adremm_mm_pro_options');
define('ADREMM_MMP_TRANS_REDIRECT','adremm_mm_pro_redirect');
define('ADREMM_MMP_TRANS_UPDATED','adremm_mm_pro_updated');

/**
 * Bootstrap all hooks after WP is ready.
 */
add_action('plugins_loaded', function () {
  /** Admin: plugin action links + row meta */
  add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
      if (!current_user_can('manage_options')) return $links;
      $settings_url = admin_url('options-general.php?page=adremm-mm-pro');
      array_unshift($links, '<a href="' . esc_url($settings_url) . '">Settings</a>');
      return $links;
  });

  add_filter('plugin_row_meta', function ($meta, $file) {
      if ($file !== plugin_basename(__FILE__)) return $meta;
      $meta[] = '<a href="' . esc_url('https://adremm.nl') . '" target="_blank" rel="noopener">ADREMM.nl</a>';
      $meta[] = '<a href="' . esc_url('https://adremm.nl/developments') . '" target="_blank" rel="noopener">Visit plugin site</a>';
      return $meta;
  }, 10, 2);


  /**
   * Markeer dat deze plugin net is geüpdatet/overschreven.
   */
  add_action('upgrader_process_complete', function ($upgrader, $hook_extra) {
      if (!is_array($hook_extra)) return;

      $action = isset($hook_extra['action']) ? (string)$hook_extra['action'] : '';
      $type   = isset($hook_extra['type']) ? (string)$hook_extra['type'] : '';
      if ($action !== 'update' || $type !== 'plugin') return;

      $plugins = $hook_extra['plugins'] ?? null;
      if (!is_array($plugins)) $plugins = array();

      $me = plugin_basename(__FILE__);
      if (in_array($me, $plugins, true)) {
          set_transient(ADREMM_MMP_TRANS_UPDATED, 1, 5 * MINUTE_IN_SECONDS);
      }
  }, 10, 2);
});

/**
 * Google Fonts lijst – dynamisch via API met caching
 */
function adremm_mmp_google_fonts() {
    $options = adremm_mmp_get_options();
    $api_key = isset($options['google_fonts_api_key']) ? trim($options['google_fonts_api_key']) : '';

    // Probeer de lijst uit de transient (cache) te halen
    $fonts = get_transient('adremm_mmp_google_fonts_list');

    if (false === $fonts) {
        $fonts = array(); // fallback wordt later gevuld

        if (!empty($api_key)) {
            $url = add_query_arg(
                array(
                    'key'   => $api_key,
                    'sort'  => 'popularity',
                    'fields' => 'items/family',
                ),
                'https://www.googleapis.com/webfonts/v1/webfonts'
            );

            $response = wp_remote_get($url, array('timeout' => 10));

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($body['items']) && is_array($body['items'])) {
                    foreach ($body['items'] as $font) {
                        $fonts[] = $font['family'];
                    }
                }
            }
        }

        // Fallback: als er geen API key is of de API faalt, gebruik een vaste lijst
        if (empty($fonts)) {
            $fonts = array(
                'Open Sans','Roboto','Lato','Montserrat','Poppins','Inter','Oswald','Raleway','Nunito','Ubuntu',
                'PT Sans','Noto Sans','Work Sans','Rubik','Merriweather','Playfair Display','Source Sans Pro','Muli',
                'Mukta','Hind','Poppins','Arimo','Quicksand','Roboto Condensed','Titillium Web','Karla','Inconsolata',
                'Fira Sans','Dosis','Cabin','Heebo','Manrope','Jost','Barlow','Exo 2','Alegreya','Libre Franklin',
                'IBM Plex Sans','PT Serif','Noto Serif','Slabo 27px','Lora','Roboto Slab','Crete Round','Zilla Slab',
                'Arvo','Cormorant Garamond','Crimson Text','Old Standard TT','Abril Fatface','Bebas Neue','Anton',
                'Oxygen','Archivo','Asap','Catamaran','Yantramanav','Krub','Maven Pro','ABeeZee','Advent Pro','Aguafina Script',
                'Aladin','Alata','Albert Sans','Alegreya Sans','Aleo','Alex Brush','Alfa Slab One','Alike','Allan',
                'Allerta','Allerta Stencil','Allura','Amatic SC','Amethysta','Amiko','Amiri','Amita','Ananda','Annie Use Your Telescope',
                'Anonymous Pro','Antic','Antic Didone','Antic Slab','Antonio','Arapey','Architects Daughter','Archivo Black',
                'Archivo Narrow','Arima Madurai','Arizonia','Armata','Artifika','Arvo','Arya','Asap Condensed',
                'Asar','Asset','Assistant','Astloch','Atma','Atomic Age','Aubrey','Audiowide','Autour One','Average',
                'Average Sans','Averia Gruesa Libre','Averia Libre','Averia Sans Libre','Averia Serif Libre','Bad Script',
                'Bahiana','Bahianita','Bai Jamjuree','Bakbak One','Ballet','Baloo 2','Baloo Bhai 2','Baloo Bhaijaan 2',
                'Baloo Bhaina 2','Baloo Chettan 2','Baloo Da 2','Baloo Paaji 2','Baloo Tamma 2','Baloo Tammudu 2',
                'Baloo Thambi 2','Balsamiq Sans','Bangers','Barlow Condensed','Barlow Semi Condensed','Barriecito',
                'Barrio','Basic','Baskervville','Battambang','Baumans','Bayon','Be Vietnam Pro','Bebas Neue',
                'Belgrano','Bellefair','Belleza','Bellota','Bellota Text','BenchNine','Benne','Bentham','Berkshire Swash',
                'Beth Ellen','Bevan','Big Shoulders Display','Big Shoulders Inline Display','Big Shoulders Inline Text',
                'Big Shoulders Stencil Display','Big Shoulders Stencil Text','Big Shoulders Text','Bilbo','Bilbo Swash Caps',
                'BioRhyme','BioRhyme Expanded','Birthstone','Birthstone Bounce','Bitter','Black And White Picture',
                'Black Han Sans','Black Ops One','Blinker','Bodoni Moda','Bokor','Bona Nova','Bonbon','Boogaloo',
                'Bowlby One','Bowlby One SC','Brawler','Bree Serif','Brygada 1918','Bubblegum Sans','Bubbler One',
                'Buda','Buenard','Bungee','Bungee Hairline','Bungee Inline','Bungee Outline','Bungee Shade','Butcherman',
                'Butterfly Kids','Cabin Condensed','Cabin Sketch','Caesar Dressing','Cagliostro','Cairo','Cairo Play',
                'Caladea','Calistoga','Calligraffitti','Cambay','Cambo','Candal','Cantarell','Cantata One','Cantora One',
                'Capriola','Caramel','Carattere','Cardo','Carme','Carrois Gothic','Carrois Gothic SC','Carter One',
                'Castoro','Catamaran','Caudex','Caveat','Caveat Brush','Cedarville Cursive','Ceviche One','Chakra Petch',
                'Changa','Changa One','Chango','Charm','Charmonman','Chathura','Chau Philomene One','Chela One',
                'Chelsea Market','Chenla','Cherish','Cherry Cream Soda','Cherry Swash','Chewy','Chicle','Chilanka',
                'Chivo','Chivo Mono','Chonburi','Cinzel','Cinzel Decorative','Clicker Script','Coda','Coda Caption',
                'Codystar','Coiny','Combo','Comfortaa','Comic Neue','Coming Soon','Commissioner','Concert One',
                'Condiment','Content','Contrail One','Convergence','Cookie','Copse','Corben','Corinthia','Cormorant',
                'Cormorant Infant','Cormorant SC','Cormorant Unicase','Cormorant Upright','Courgette','Courier Prime',
                'Cousine','Coustard','Covered By Your Grace','Crafty Girls','Creepster','Crete Round','Crimson Pro',
                'Croissant One','Crushed','Cuprum','Cute Font','Cutive','Cutive Mono','DM Mono','DM Sans','DM Serif Display',
                'DM Serif Text','Damion','Dancing Script','Dangrek','Darker Grotesque','David Libre','Dawning of a New Day',
                'Days One','Dekko','Dela Gothic One','Delius','Delius Swash Caps','Delius Unicase','Della Respira',
                'Denk One','Devonshire','Dhurjati','Didact Gothic','Diplomata','Diplomata SC','Do Hyeon','Dokdo',
                'Domine','Donegal One','Doppio One','Dorsa','Dosis','DotGothic16','Dr Sugiyama','Duru Sans','Dynalight',
                'Eagle Lake','East Sea Dokdo','Eater','EB Garamond','Economica','Eczar','El Messiri','Electrolize',
                'Elsie','Elsie Swash Caps','Emblema One','Emilys Candy','Encode Sans','Encode Sans Condensed',
                'Encode Sans Expanded','Encode Sans Semi Condensed','Encode Sans Semi Expanded','Engagement',
                'Englebert','Enriqueta','Ephesis','Epilogue','Erica One','Esteban','Estonia','Euphoria Script',
                'Ewert','Exo','Exo 2','Expletus Sans','Explora','Fahkwang','Fanwood Text','Farro','Farsan','Fascinate',
                'Fascinate Inline','Faster One','Fasthand','Fauna One','Faustina','Federant','Federo','Felipa',
                'Fenix','Festive','Finger Paint','Finlandica','Fira Code','Fira Mono','Fira Sans','Fira Sans Condensed',
                'Fira Sans Extra Condensed','Fjalla One','Fjord One','Flamenco','Flavors','Fleur De Leah','Flow Block',
                'Flow Circular','Flow Rounded','Fondamento','Fontdiner Swanky','Forum','Fragment Mono','Francois One',
                'Frank Ruhl Libre','Fraunces','Fredericka the Great','Fredoka','Fredoka One','Freehand','Fresca',
                'Frijole','Fruktur','Fugaz One','Fuggles','Fustat','Fuzzy Bubbles','GFS Didot','GFS Neohellenic',
                'Gabarito','Gabriela','Gaegu','Gafata','Galada','Galdeano','Galindo','Gamja Flower','Gantari',
                'Gayathri','Gelasio','Gemunu Libre','Genos','Gentium Basic','Gentium Book Basic','Gentium Plus',
                'Geo','Georama','Geostar','Geostar Fill','Germania One','Gideon Roman','Gidugu','Gilda Display',
                'Girassol','Give You Glory','Glass Antiqua','Glegoo','Gloria Hallelujah','Glory','Gluten',
                'Goblin One','Gochi Hand','Goldman','Gorditas','Gothic A1','Gotu','Goudy Bookletter 1911',
                'Gowun Batang','Gowun Dodum','Graduate','Grand Hotel','Grandstander','Gravitas One','Great Vibes',
                'Grechen Fuemen','Grenze','Grenze Gotisch','Grey Qo','Griffy','Gruppo','Gudea','Gugi','Gulzar',
                'Gupter','Gurajada','Gwendolyn','Habibi','Hachi Maru Pop','Hahmlet','Halant','Hammersmith One',
                'Hanalei','Hanalei Fill','Handlee','Hanuman','Happy Monkey','Harmattan','Headland One','Heebo',
                'Hedvig Letters Sans','Hedvig Letters Serif','Hepta Slab','Herr Von Muellerhoff','Hi Melody',
                'Hina Mincho','Hind','Hind Guntur','Hind Madurai','Hind Siliguri','Hind Vadodara','Holtwood One SC',
                'Homemade Apple','Homenaje','Hubballi','Hudson NY','Hurricane','IBM Plex Mono','IBM Plex Sans',
                'IBM Plex Sans Arabic','IBM Plex Sans Condensed','IBM Plex Sans Devanagari','IBM Plex Sans Hebrew',
                'IBM Plex Sans JP','IBM Plex Sans KR','IBM Plex Sans Thai','IBM Plex Sans Thai Looped','IBM Plex Serif',
                'IM Fell DW Pica','IM Fell DW Pica SC','IM Fell Double Pica','IM Fell Double Pica SC','IM Fell English',
                'IM Fell English SC','IM Fell French Canon','IM Fell French Canon SC','IM Fell Great Primer',
                'IM Fell Great Primer SC','Ibarra Real Nova','Iceberg','Iceland','Imbue','Imperial Script',
                'Imprima','Inconsolata','Inder','Indie Flower','Ingrid Darling','Inika','Inknut Antiqua',
                'Inria Sans','Inria Serif','Inspiration','Instrument Sans','Instrument Serif','Inter Tight',
                'Irish Grover','Island Moments','Istok Web','Italiana','Italianno','Itim','Jacques Francois',
                'Jacques Francois Shadow','Jaldi','JetBrains Mono','Jim Nightshade','Joan','Jockey One',
                'Jolly Lodger','Jomhuria','Jomolhari','Josefin Sans','Josefin Slab','Jost','Joti One','Jua',
                'Judson','Julee','Julius Sans One','Junge','Jura','Just Another Hand','Just Me Again Down Here',
                'K2D','Kadwa','Kaisei Decol','Kaisei HarunoUmi','Kaisei Opti','Kaisei Tokumin','Kalam','Kameron',
                'Kanit','Kantumruy','Kantumruy Pro','Karantina','Karla','Karma','Katibeh','Kaushan Script',
                'Kavivanar','Kavoon','Kay Pho Du','Kdam Thmor Pro','Keania One','Kelly Slab','Kenia','Khand',
                'Khmer','Khula','Kings','Kirang Haerang','Kite One','Kiwi Maru','Klee One','Knewave','KoHo',
                'Kodchasan','Koh Santepheap','Kolker Brush','Konkhmer Sleokchher','Kosugi','Kosugi Maru',
                'Kotta One','Koulen','Kranky','Kreon','Kristi','Krona One','Krub','Kufam','Kulim Park',
                'Kumar One','Kumar One Outline','Kumbh Sans','Kurale','La Belle Aurore','Lacquer','Laila',
                'Lakki Reddy','Lalezar','Lancelot','Langar','Lateef','Lato','Lavishly Yours','League Gothic',
                'League Script','League Spartan','Leckerli One','Ledger','Lekton','Lemon','Lemonada','Lexend',
                'Lexend Deca','Lexend Exa','Lexend Giga','Lexend Mega','Lexend Peta','Lexend Tera','Lexend Zetta',
                'Libre Barcode 128','Libre Barcode 128 Text','Libre Barcode 39','Libre Barcode 39 Extended',
                'Libre Barcode 39 Extended Text','Libre Barcode 39 Text','Libre Baskerville','Libre Bodoni',
                'Libre Caslon Display','Libre Caslon Text','Libre Franklin','Licorice','Life Savers','Lilita One',
                'Lily Script One','Limelight','Linden Hill','Liter','Liu Jian Mao Cao','Livvic','Lobster',
                'Lobster Two','Londrina Outline','Londrina Shadow','Londrina Sketch','Londrina Solid','Long Cang',
                'Lora','Love Light','Love Ya Like A Sister','Loved by the King','Lovers Quarrel','Luckiest Guy',
                'Lusitana','Lustria','Luxurious Roman','Luxurious Script','M PLUS 1','M PLUS 1 Code','M PLUS 1p',
                'M PLUS 2','M PLUS Code Latin','M PLUS Rounded 1c','Ma Shan Zheng','Macondo','Macondo Swash Caps',
                'Mada','Magra','Maiden Orange','Maitree','Major Mono Display','Mako','Mali','Mallanna','Mandali',
                'Manjari','Manrope','Mansalva','Manuale','Marcellus','Marcellus SC','Marck Script','Margarine',
                'Marhey','Markazi Text','Marko One','Marmelad','Martel','Martel Sans','Martian Mono','Marvel',
                'Mate','Mate SC','Maven Pro','McLaren','Mea Culpa','Meddon','MedievalSharp','Medula One',
                'Meera Inimai','Megrim','Meie Script','Meow Script','Merienda','Merienda One','Merriweather',
                'Merriweather Sans','Metal','Metal Mania','Metamorphous','Metrophobic','Michroma','Milonga',
                'Miltonian','Miltonian Tattoo','Mina','Mingzat','Miniver','Miriam Libre','Mirza','Miss Fajardose',
                'Mitr','Mochiy Pop One','Mochiy Pop P One','Modak','Modern Antiqua','Mogra','Mohave','Molengo',
                'Molle','Monda','Monofett','Monoton','Monsieur La Doulaise','Montaga','Montagu Slab','MonteCarlo',
                'Montez','Montserrat Alternates','Montserrat Subrayada','Moo Lah Lah','Moon Dance','Moul',
                'Moulpali','Mountains of Christmas','Mouse Memoirs','Mr Bedfort','Mr Dafoe','Mrs Saint Delafield',
                'Mrs Sheppards','Ms Madi','Mukta','Mukta Mahee','Mukta Malar','Mukta Vaani','Mulish','Murecho',
                'MuseoModerno','Mystery Quest','NTR','Nabla','Nanum Brush Script','Nanum Gothic','Nanum Gothic Coding',
                'Nanum Myeongjo','Nanum Pen Script','Narnoor','Neonderthaw','Nerko One','Neucha','Neuton',
                'New Rocker','New Tegomin','News Cycle','Newsreader','Niconne','Niramit','Nixie One','Nobile',
                'Nokora','Norican','Nosifer','Notable','Nothing You Could Do','Noticia Text','Noto Color Emoji',
                'Noto Emoji','Noto Kufi Arabic','Noto Music','Noto Naskh Arabic','Noto Nastaliq Urdu',
                'Noto Rashi Hebrew','Noto Sans Adlam','Noto Sans Adlam Unjoined','Noto Sans Anatolian Hieroglyphs',
                'Noto Sans Arabic','Noto Sans Armenian','Noto Sans Avestan','Noto Sans Balinese','Noto Sans Bamum',
                'Noto Sans Bassa Vah','Noto Sans Batak','Noto Sans Bengali','Noto Sans Bhaiksuki','Noto Sans Brahmi',
                'Noto Sans Buginese','Noto Sans Buhid','Noto Sans Canadian Aboriginal','Noto Sans Carian',
                'Noto Sans Caucasian Albanian','Noto Sans Chakma','Noto Sans Cham','Noto Sans Cherokee',
                'Noto Sans Coptic','Noto Sans Cuneiform','Noto Sans Cypriot','Noto Sans Deseret',
                'Noto Sans Devanagari','Noto Sans Display','Noto Sans Duployan','Noto Sans Egyptian Hieroglyphs',
                'Noto Sans Elbasan','Noto Sans Elymaic','Noto Sans Ethiopic','Noto Sans Georgian',
                'Noto Sans Glagolitic','Noto Sans Gothic','Noto Sans Grantha','Noto Sans Gujarati',
                'Noto Sans Gunjala Gondi','Noto Sans Gurmukhi','Noto Sans Hanifi Rohingya','Noto Sans Hanunoo',
                'Noto Sans Hatran','Noto Sans Hebrew','Noto Sans Imperial Aramaic','Noto Sans Indic Siyaq Numbers',
                'Noto Sans Inscriptional Pahlavi','Noto Sans Inscriptional Parthian','Noto Sans Javanese',
                'Noto Sans Kaithi','Noto Sans Kannada','Noto Sans Kayah Li','Noto Sans Kharoshthi','Noto Sans Khmer',
                'Noto Sans Khojki','Noto Sans Khudawadi','Noto Sans Lao','Noto Sans Lao Looped','Noto Sans Lepcha',
                'Noto Sans Limbu','Noto Sans Linear A','Noto Sans Linear B','Noto Sans Lisu','Noto Sans Lycian',
                'Noto Sans Lydian','Noto Sans Mahajani','Noto Sans Malayalam','Noto Sans Mandaic',
                'Noto Sans Manichaean','Noto Sans Marchen','Noto Sans Masaram Gondi','Noto Sans Math',
                'Noto Sans Mayan Numerals','Noto Sans Medefaidrin','Noto Sans Meetei Mayek',
                'Noto Sans Mende Kikakui','Noto Sans Meroitic','Noto Sans Miao','Noto Sans Modi',
                'Noto Sans Mongolian','Noto Sans Mono','Noto Sans Mro','Noto Sans Multani','Noto Sans Myanmar',
                'Noto Sans N Ko','Noto Sans Nabataean','Noto Sans New Tai Lue','Noto Sans Newa',
                'Noto Sans Nushu','Noto Sans Ogham','Noto Sans Ol Chiki','Noto Sans Old Hungarian',
                'Noto Sans Old Italic','Noto Sans Old North Arabian','Noto Sans Old Permic',
                'Noto Sans Old Persian','Noto Sans Old Sogdian','Noto Sans Old South Arabian',
                'Noto Sans Old Turkic','Noto Sans Oriya','Noto Sans Osage','Noto Sans Osmanya',
                'Noto Sans Pahawh Hmong','Noto Sans Palmyrene','Noto Sans Pau Cin Hau','Noto Sans Phags Pa',
                'Noto Sans Phoenician','Noto Sans Psalter Pahlavi','Noto Sans Rejang','Noto Sans Runic',
                'Noto Sans Samaritan','Noto Sans Saurashtra','Noto Sans Sharada','Noto Sans Shavian',
                'Noto Sans Siddham','Noto Sans Sinhala','Noto Sans Sogdian','Noto Sans Sora Sompeng',
                'Noto Sans Soyombo','Noto Sans Sundanese','Noto Sans Syloti Nagri','Noto Sans Symbols',
                'Noto Sans Symbols 2','Noto Sans Syriac','Noto Sans Tagalog','Noto Sans Tagbanwa',
                'Noto Sans Tai Le','Noto Sans Tai Tham','Noto Sans Tai Viet','Noto Sans Takri','Noto Sans Tamil',
                'Noto Sans Tamil Supplement','Noto Sans Telugu','Noto Sans Thaana','Noto Sans Thai',
                'Noto Sans Thai Looped','Noto Sans Tifinagh','Noto Sans Tirhuta','Noto Sans Ugaritic',
                'Noto Sans Vai','Noto Sans Wancho','Noto Sans Warang Citi','Noto Sans Yi','Noto Sans Zawgyi',
                'Noto Serif','Noto Serif Ahom','Noto Serif Armenian','Noto Serif Balinese','Noto Serif Bengali',
                'Noto Serif Devanagari','Noto Serif Display','Noto Serif Dogra','Noto Serif Ethiopic',
                'Noto Serif Georgian','Noto Serif Grantha','Noto Serif Gujarati','Noto Serif Gurmukhi',
                'Noto Serif Hebrew','Noto Serif Kannada','Noto Serif Khmer','Noto Serif Khojki',
                'Noto Serif Lao','Noto Serif Makasar','Noto Serif Malayalam','Noto Serif Myanmar',
                'Noto Serif NP Hmong','Noto Serif Oriya','Noto Serif Sinhala','Noto Serif Tamil',
                'Noto Serif Tangut','Noto Serif Telugu','Noto Serif Thai','Noto Serif Tibetan',
                'Noto Serif Toto','Noto Serif Yezidi','Nova Cut','Nova Flat','Nova Mono','Nova Oval',
                'Nova Round','Nova Script','Nova Slim','Nova Square','Numans','Nunito Sans',
                'Nuosu SIL','Odibee Sans','Odor Mean Chey','Offside','Oi','Old Standard TT','Oldenburg',
                'Ole','Oleo Script','Oleo Script Swash Caps','Oooh Baby','Open Sans Condensed',
                'Oranienbaum','Orbitron','Oregano','Orelega One','Orienta','Original Surfer',
                'Oswald','Otomanopee One','Outfit','Over the Rainbow','Overlock','Overlock SC',
                'Overpass','Overpass Mono','Ovo','Oxanium','Oxygen','Oxygen Mono','PT Mono',
                'PT Sans Caption','PT Sans Narrow','PT Serif Caption','Pacifico','Padauk',
                'Padyakke Expanded One','Palette Mosaic','Palanquin','Palanquin Dark','Pangolin',
                'Paprika','Parisienne','Passero One','Passion One','Passions Conflict',
                'Pathway Extreme','Pathway Gothic One','Patrick Hand','Patrick Hand SC','Pattaya',
                'Patua One','Pavanam','Paytone One','Peddana','Peralta','Permanent Marker',
                'Petemoss','Petit Formal Script','Petrona','Philosopher','Piazzolla','Piedra',
                'Pinyon Script','Pirata One','Plaster','Play','Playball','Playfair Display SC',
                'Plus Jakarta Sans','Podkova','Poiret One','Poller One','Poly','Pompiere',
                'Pontano Sans','Poor Story','Poppins','Port Lligat Sans','Port Lligat Slab',
                'Potta One','Pragati Narrow','Praise','Prata','Preahvihear','Press Start 2P',
                'Pridi','Princess Sofia','Prociono','Prompt','Prosto One','Proza Libre',
                'Public Sans','Puppies Play','Puritan','Purple Purse','Qahiri','Quando',
                'Quantico','Quattrocento','Quattrocento Sans','Questrial','Quicksand',
                'Quintessential','Qwigley','Qwitcher Grypen','Racing Sans One','Radley',
                'Rajdhani','Rakkas','Raleway Dots','Ramabhadra','Ramaraja','Rambla',
                'Rammetto One','Rampart One','Ranchers','Rancho','Ranga','Rasa','Rationale',
                'Ravi Prakash','Readex Pro','Recursive','Red Hat Display','Red Hat Mono',
                'Red Hat Text','Red Rose','Redacted','Redacted Script','Redressed','Reem Kufi',
                'Reem Kufi Fun','Reem Kufi Ink','Reenie Beanie','Reggae One','Reggae One',
                'Revalia','Rhodium Libre','Ribeye','Ribeye Marrow','Righteous','Risque',
                'Road Rage','Roboto Condensed','Roboto Flex','Roboto Mono','Roboto Serif',
                'Roboto Slab','Rochester','Rock 3D','Rock Salt','RocknRoll One','Rokkitt',
                'Romanesco','Ropa Sans','Rosario','Rosarivo','Rouge Script','Rowdies',
                'Rozha One','Rubik 80s Fade','Rubik Beastly','Rubik Bubbles','Rubik Burned',
                'Rubik Dirt','Rubik Distressed','Rubik Glitch','Rubik Iso','Rubik Marker Hatch',
                'Rubik Maze','Rubik Microbe','Rubik Mono One','Rubik Moonrocks','Rubik Pixels',
                'Rubik Puddles','Rubik Spray Paint','Rubik Storm','Rubik Vinyl','Rubik Wet Paint',
                'Ruda','Rufina','Ruge Boogie','Ruluko','Rum Raisin','Ruslan Display','Russo One',
                'Ruthie','Ryder','Rye','STIX Two Text','Sacramento','Sahitya','Sail','Saira',
                'Saira Condensed','Saira Extra Condensed','Saira Semi Condensed',
                'Saira Stencil One','Salsa','Sanchez','Sancreek','Sansita','Sansita Swashed',
                'Sarabun','Sarala','Sarina','Sarpanch','Sassy Frass','Satisfy',
                'Sawarabi Gothic','Sawarabi Mincho','Scada','Scheherazade New','Schoolbell',
                'Scope One','Seaweed Script','Secular One','Sedgwick Ave','Sedgwick Ave Display',
                'Sen','Send Flowers','Sevillana','Seymour One','Shadows Into Light',
                'Shadows Into Light Two','Shalimar','Shanti','Share','Share Tech','Share Tech Mono',
                'Shippori Antique','Shippori Antique B1','Shippori Mincho','Shippori Mincho B1',
                'Shizuru','Shojumaru','Short Stack','Shrikhand','Siemreap','Sigmar','Sigmar One',
                'Signika','Signika Negative','Silkscreen','Simonetta','Single Day','Sintony',
                'Sirin Stencil','Six Caps','Skranji','Slabo 13px','Slabo 27px','Slackey',
                'Slackside One','Smokum','Smooch','Smooch Sans','Smythe','Sniglet','Snippet',
                'Snowburst One','Sofadi One','Sofia','Sofia Sans','Sofia Sans Condensed',
                'Sofia Sans Extra Condensed','Sofia Sans Semi Condensed','Solitreo','Solway',
                'Sometype Mono','Song Myung','Sono','Sonsie One','Sora','Sorts Mill Goudy',
                'Source Code Pro','Source Sans 3','Source Sans Pro','Source Serif 4',
                'Source Serif Pro','Space Grotesk','Space Mono','Spartan','Special Elite',
                'Spectral','Spectral SC','Spicy Rice','Spinnaker','Spirax','Splash',
                'Spline Sans','Spline Sans Mono','Squada One','Sree Krushnadevaraya',
                'Staatliches','Stalemate','Stalinist One','Stardos Stencil','Stick',
                'Stick No Bills','Stint Ultra Condensed','Stint Ultra Expanded','Stoke',
                'Strait','Style Script','Stylish','Sue Ellen Francisco','Suez One',
                'Sulphur Point','Sumana','Sunflower','Sunshiney','Supermercado One',
                'Sura','Suranna','Suravaram','Suwannaphum','Swanky and Moo Moo',
                'Syncopate','Syne','Syne Mono','Syne Tactile','Tajawal','Tangerine',
                'Taprom','Tauri','Taviraj','Teko','Telex','Tenali Ramakrishna',
                'Tenor Sans','Text Me One','Texturina','Thasadith','The Girl Next Door',
                'The Nautigal','Tienne','Tillana','Tilt Neon','Tilt Prism','Tilt Warp',
                'Timmana','Tinos','Tiro Bangla','Tiro Devanagari Hindi',
                'Tiro Devanagari Marathi','Tiro Devanagari Sanskrit','Tiro Gurmukhi',
                'Tiro Kannada','Tiro Tamil','Tiro Telugu','Titan One','Titillium Web',
                'Tomorrow','Tourney','Trade Winds','Train One','Trirong','Trispace',
                'Trocchi','Trochut','Truculenta','Trykker','Tsukimi Rounded','Tulpen One',
                'Turret Road','Twinkle Star','Ubuntu Condensed','Ubuntu Mono','Uchen',
                'Ultra','Unbounded','Uncial Antiqua','Underdog','Unica One',
                'UnifrakturCook','UnifrakturMaguntia','Unkempt','Unlock','Unna',
                'Updock','Urbanist','VT323','Vampiro One','Varela','Varela Round',
                'Varta','Vast Shadow','Vesper Libre','Viaoda Libre','Vibes','Vibur',
                'Vidaloka','Viga','Voces','Volkhov','Vollkorn','Vollkorn SC',
                'Voltaire','Vujahday Script','Waiting for the Sunrise','Wallpoet',
                'Walter Turncoat','Warnes','Water Brush','Waterfall','Wellfleet',
                'Wendy One','Whisper','WindSong','Wire One','Wix Madefor Display',
                'Wix Madefor Text','Workbench','Xanh Mono','Yaldevi','Yanone Kaffeesatz',
                'Yantramanav','Yarndings 12','Yarndings 12 Charted','Yatra One',
                'Yellowtail','Yeon Sung','Yeseva One','Yesteryear','Yomogi','Yrsa',
                'Ysabeau','Ysabeau Infant','Ysabeau Office','Ysabeau SC','Yuji Boku',
                'Yuji Hentaigana Akari','Yuji Hentaigana Akebono','Yuji Mai','Yuji Syuku',
                'Yusei Magic','ZCOOL KuaiLe','ZCOOL QingKe HuangYou','ZCOOL XiaoWei',
                'Zen Antique','Zen Antique Soft','Zen Dots','Zen Kaku Gothic Antique',
                'Zen Kaku Gothic New','Zen Kurenaido','Zen Loop','Zen Maru Gothic',
                'Zen Old Mincho','Zen Tokyo Zoo','Zeyada','Zhi Mang Xing','Zilla Slab',
                'Zilla Slab Highlight'
            );
        }

        // Sorteer alfabetisch
        sort($fonts);

        // Sla op in cache voor 7 dagen (of korter als er geen API key is)
        $cache_duration = empty($api_key) ? DAY_IN_SECONDS : 7 * DAY_IN_SECONDS;
        set_transient('adremm_mmp_google_fonts_list', $fonts, $cache_duration);
    }

    return $fonts;
}

function adremm_mmp_social_platforms(){
  // 15 meest gebruikte (simple, stabiele icon set; custom icon URL kan per item)
  $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 3a7 7 0 1 1-7 7 7 7 0 0 1 7-7z"/></svg>';
  return array(
    'facebook'  => array('label'=>'Facebook',  'svg'=>$icon),
    'instagram' => array('label'=>'Instagram', 'svg'=>$icon),
    'linkedin'  => array('label'=>'LinkedIn',  'svg'=>$icon),
    'x'         => array('label'=>'X',         'svg'=>$icon),
    'youtube'   => array('label'=>'YouTube',   'svg'=>$icon),
    'tiktok'    => array('label'=>'TikTok',    'svg'=>$icon),
    'pinterest' => array('label'=>'Pinterest', 'svg'=>$icon),
    'snapchat'  => array('label'=>'SnapChat',  'svg'=>$icon),
    'whatsapp'  => array('label'=>'WhatsApp',  'svg'=>$icon),
    'telegram'  => array('label'=>'Telegram',  'svg'=>$icon),
    'discord'   => array('label'=>'Discord',   'svg'=>$icon),
    'github'    => array('label'=>'GitHub',    'svg'=>$icon),
    'behance'   => array('label'=>'Behance',   'svg'=>$icon),
    'dribbble'  => array('label'=>'Dribbble',  'svg'=>$icon),
    'threads'   => array('label'=>'Threads',   'svg'=>$icon),
  );
}


function adremm_mmp_units(){
  return array('px'=>'px','em'=>'em','rem'=>'rem');
}

function adremm_mmp_unit_select($name, $current, $units){
  $current = $current ? $current : 'px';
  if(!is_array($units) || empty($units)){
    $units = adremm_mmp_units();
  }
  $out = '<select class="adremm-unit" name="'.esc_attr(ADREMM_MMP_OPT).'['.esc_attr($name).']">';
  foreach($units as $u=>$t){
    $out .= '<option value="'.esc_attr($u).'" '.selected($current, $u, false).'>'.esc_html($t).'</option>';
  }
  $out .= '</select>';
  return $out;
}


function adremm_mmp_defaults(){
  $layer = function(){
    return array(
      'mode' => 'theme',  // theme|google|custom
      'family' => 'inherit',
      'size' => '16',
      'unit' => 'px',     // px|rem|em
      'weight' => '500',
      'line' => '1.2',
      'color' => 'rgba(17,17,17,1)',
      'hover' => '',
      'active' => '',
      'pad_y' => '10',
      'pad_x' => '12',
      'gap'   => '8',
      'underline' => 'none', // none|solid|dashed|dotted|double
      'ul_thick' => '2',
      'ul_offset'=> '3'
    );
  };

  $social_defaults = array();
  foreach(adremm_mmp_social_platforms() as $k=>$v){
    $social_defaults[$k] = array('on'=>'0','url'=>'','custom_icon_url'=>'');
  }

  return array(
    'label'       => 'Menu',
    'back_label'  => 'Terug',
    'z_index'     => '999999',
    'global_font' => 'inherit',
    'drawer'      => 'right',
    'easing'      => 'ease-in-out',
    'breakpoint'  => '980',
    'breakpoint_unit' => 'px',
    'menu_src'    => 'auto',

    // Toggle / label
    'toggle_align'=> 'right',   // left|center|right
    'toggle_fixed'=> '0',       // 0|1
    'toggle_top'  => '20',      // px
    'toggle_side' => '20',      // px
    'toggle_top_unit'  => 'px',
    'toggle_side_unit' => 'px',
    'toggle_bg'   => 'rgba(255,255,255,0)',

    // Global font (used when layer mode = theme)
    'global_font' => 'inherit',


    // Paneel sizing
    'panel_w'     => '90vw',
    'panel_max'   => '420px',
    'fs_nav_max'  => '520px',

    'panel_bg'    => 'rgba(255,255,255,1)',
    'panel_border'=> 'rgba(0,0,0,0.12)',
    'panel_border_width' => '1', // in px, 0 = geen rand
    'overlay'     => 'rgba(0,0,0,0.45)',

    'burger_off'  => 'rgba(17,17,17,1)',
    'burger_on'   => 'rgba(214,33,33,1)',
    'burger_anim' => 'swap',

    'close_size'  => '16',
    'close_unit'  => 'px',
    'close_color' => 'rgba(17,17,17,1)',
    'back_position' => 'header', // header|footer|replace_close
    'show_close'    => '1',

    'submenu_icon'      => 'chevron', // chevron|caret|plus|custom
    'submenu_icon_url'  => '',
    'submenu_icon_size' => '14',
    'submenu_icon_unit' => 'px',
    'back_icon'         => 'chevron', // chevron|caret|plus|custom
    'back_icon_url'     => '',
    'back_icon_size'    => '14',
    'back_icon_unit' => 'px',

    'layers' => array(
      'menu_label' => $layer(),
      'items'      => $layer(),
      'subitems'   => $layer(),
      'back'       => $layer(),
    ),

    'socials_on'   => '0',
    'socials_size' => '25',
    'socials_unit' => 'px',
    'socials'      => $social_defaults,

    'credits_on'   => '0',
    'credits_text' => 'ADREMM',
    'credits_url'  => 'https://adremm.nl',
    'credits_icon_url' => '',
    'credits_size' => '25',
    'credits_unit' => 'px',

    // Google Fonts API key
    'google_fonts_api_key' => '',
  );
}

function adremm_mmp_get_options(){
  $d = adremm_mmp_defaults();
  $o = get_option(ADREMM_MMP_OPT, array());
  if(!is_array($o)) $o = array();
  $m = array_merge($d, $o);

  if(!isset($m['layers']) || !is_array($m['layers'])) $m['layers'] = $d['layers'];
  foreach($d['layers'] as $k=>$lv){
    if(!isset($m['layers'][$k]) || !is_array($m['layers'][$k])) $m['layers'][$k] = $lv;
    else $m['layers'][$k] = array_merge($lv, $m['layers'][$k]);
  }

  if(!isset($m['socials']) || !is_array($m['socials'])) $m['socials'] = $d['socials'];
  foreach($d['socials'] as $k=>$sv){
    if(!isset($m['socials'][$k]) || !is_array($m['socials'][$k])) $m['socials'][$k] = $sv;
    else $m['socials'][$k] = array_merge($sv, $m['socials'][$k]);
  }
  return $m;
}

/**
 * Redirect handling: activation + overwrite update.
 */
register_activation_hook(__FILE__, function () {
    if (!is_admin()) return;
    set_transient(ADREMM_MMP_TRANS_UPDATED, 1, 60);
});

add_action('upgrader_process_complete', function ($upgrader, $hook_extra) {
    if (empty($hook_extra['type']) || $hook_extra['type'] !== 'plugin') return;

    $plugins = [];
    if (!empty($hook_extra['plugins']) && is_array($hook_extra['plugins'])) {
        $plugins = $hook_extra['plugins'];
    } elseif (!empty($hook_extra['plugin'])) {
        $plugins = [(string)$hook_extra['plugin']];
    }

    if (in_array(plugin_basename(__FILE__), $plugins, true)) {
        set_transient(ADREMM_MMP_TRANS_UPDATED, 1, 5 * MINUTE_IN_SECONDS);
    }
}, 10, 2);

add_action('admin_init', function () {
    if (!current_user_can('manage_options')) return;
    if (wp_doing_ajax()) return;

    $flag = get_transient(ADREMM_MMP_TRANS_UPDATED);
    if (!$flag) return;

    // Controleer of we al op de settingspagina zijn
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $settings_url = admin_url('options-general.php?page=adremm-mm-pro');
    if (strpos($current_url, $settings_url) !== false) {
        delete_transient(ADREMM_MMP_TRANS_UPDATED);
        return;
    }

    delete_transient(ADREMM_MMP_TRANS_UPDATED);
    wp_safe_redirect($settings_url);
    exit;
});

add_action('admin_init', function(){
  register_setting(ADREMM_MMP_OPT, ADREMM_MMP_OPT, array(
    'type'=>'array',
    'sanitize_callback'=>'adremm_mmp_sanitize',
    'default'=>adremm_mmp_defaults(),
  ));
});

function adremm_mmp_sanitize_rgba($v){
  $v = trim((string)$v);
  if($v==='') return '';
  if(preg_match('/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*(0(\.\d+)?|1(\.0+)?)\s*\)$/', $v)) return $v;
  if(preg_match('/^rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/', $v)){
    $nums = preg_replace('/[^0-9,]/','',$v);
    $p = explode(',',$nums);
    if(count($p)===3) return 'rgba('.intval($p[0]).','.intval($p[1]).','.intval($p[2]).',1)';
  }
  if(preg_match('/^#?[0-9a-fA-F]{6}$/', $v)){
    $h = ltrim($v,'#');
    $r = hexdec(substr($h,0,2)); $g=hexdec(substr($h,2,2)); $b=hexdec(substr($h,4,2));
    return 'rgba('.$r.','.$g.','.$b.',1)';
  }
  return '';
}

function adremm_mmp_sanitize_css_len($v, $fallback){
  $v = trim((string)$v);
  if($v==='') return $fallback;
  // Accept: 123px | 1.2rem | 50vw | 100% | 0
  if(preg_match('/^(0|[0-9]+(\.[0-9]+)?)(px|rem|em|vw|vh|%){0,1}$/', $v)) return $v;
  return $fallback;
}

function adremm_mmp_sanitize_layer($in, $d){
  if(!is_array($in)) $in=array();
  $out = $d;
  $out['mode'] = in_array($in['mode'] ?? 'theme', array('theme','google','custom'), true) ? $in['mode'] : 'theme';
  $out['family'] = sanitize_text_field($in['family'] ?? 'inherit');
  $out['size'] = preg_replace('/[^0-9]/','', (string)($in['size'] ?? $d['size']));
  $out['unit'] = in_array(($in['unit'] ?? 'px'), array('px','rem','em'), true) ? $in['unit'] : 'px';
  $out['weight'] = preg_replace('/[^0-9]/','', (string)($in['weight'] ?? $d['weight']));
  $out['line'] = preg_replace('/[^0-9.]/','', (string)($in['line'] ?? $d['line']));
  $out['color'] = adremm_mmp_sanitize_rgba($in['color'] ?? $d['color']);
  $out['hover'] = adremm_mmp_sanitize_rgba($in['hover'] ?? '');
  $out['active']= adremm_mmp_sanitize_rgba($in['active'] ?? '');
  $out['pad_y'] = preg_replace('/[^0-9]/','', (string)($in['pad_y'] ?? $d['pad_y']));
  $out['pad_x'] = preg_replace('/[^0-9]/','', (string)($in['pad_x'] ?? $d['pad_x']));
  $out['gap']   = preg_replace('/[^0-9]/','', (string)($in['gap'] ?? $d['gap']));
  $out['underline'] = in_array(($in['underline'] ?? 'none'), array('none','solid','dashed','dotted','double'), true) ? $in['underline'] : 'none';
  $out['ul_thick'] = preg_replace('/[^0-9]/','', (string)($in['ul_thick'] ?? $d['ul_thick']));
  $out['ul_offset']= preg_replace('/[^0-9]/','', (string)($in['ul_offset'] ?? $d['ul_offset']));
  return $out;
}

function adremm_mmp_sanitize($in){
  $d = adremm_mmp_defaults();
  if(!is_array($in)) $in=array();
  $out = array();

  $simple = array(
    'label','back_label','z_index','drawer','easing','breakpoint','menu_src','toggle_align','toggle_fixed','toggle_top','toggle_side','toggle_bg','global_font',
    'panel_w','panel_max','fs_nav_max',
    'panel_bg','panel_border','panel_border_width','overlay',
    'burger_off','burger_on','burger_anim',
    'close_size','close_color','back_position','show_close',
    'submenu_icon','submenu_icon_url','submenu_icon_size',
    'back_icon','back_icon_url','back_icon_size',
    'socials_on','socials_size',
    'credits_on','credits_text','credits_url','credits_icon_url','credits_size',
    'google_fonts_api_key'
  );

  foreach($simple as $k){
    $v = isset($in[$k]) ? wp_unslash($in[$k]) : ($d[$k] ?? '');
    $out[$k] = is_string($v) ? sanitize_text_field($v) : $v;
  }

  foreach(array('breakpoint','z_index','submenu_icon_size','back_icon_size','close_size','socials_size','credits_size','panel_border_width') as $nk){
    $out[$nk] = preg_replace('/[^0-9]/','', (string)($out[$nk] ?? ''));
  }

  $out['panel_w']    = adremm_mmp_sanitize_css_len($out['panel_w'] ?? $d['panel_w'], $d['panel_w']);
  $out['panel_max']  = adremm_mmp_sanitize_css_len($out['panel_max'] ?? $d['panel_max'], $d['panel_max']);
  $out['fs_nav_max'] = adremm_mmp_sanitize_css_len($out['fs_nav_max'] ?? $d['fs_nav_max'], $d['fs_nav_max']);

  foreach(array('panel_bg','panel_border','overlay','burger_off','burger_on','close_color') as $ck){
    $out[$ck] = adremm_mmp_sanitize_rgba($out[$ck] ?? $d[$ck]);
    if($out[$ck]==='') $out[$ck] = $d[$ck];
  }

  $out['layers'] = array();
  $din = isset($in['layers']) && is_array($in['layers']) ? $in['layers'] : array();
  foreach($d['layers'] as $lk=>$ld){
    $out['layers'][$lk] = adremm_mmp_sanitize_layer($din[$lk] ?? array(), $ld);
  }

  $out['socials'] = array();
  $sin = isset($in['socials']) && is_array($in['socials']) ? $in['socials'] : array();
  foreach($d['socials'] as $sk=>$sd){
    $row = isset($sin[$sk]) && is_array($sin[$sk]) ? $sin[$sk] : array();
    $out['socials'][$sk] = array(
      'on' => (!empty($row['on']) && $row['on']!=='0') ? '1' : '0',
      'url' => esc_url_raw($row['url'] ?? ''),
      'custom_icon_url' => esc_url_raw($row['custom_icon_url'] ?? '')
    );
  }

  if(!in_array($out['drawer'], array('left','right','fullscreen'), true)) $out['drawer'] = $d['drawer'];
  if(!in_array($out['submenu_icon'], array('chevron','caret','plus','custom'), true)) $out['submenu_icon'] = $d['submenu_icon'];
  if(!in_array($out['back_icon'], array('chevron','caret','plus','custom'), true)) $out['back_icon'] = $d['back_icon'];
  if(!in_array($out['burger_anim'], array('swap','squeeze','morph','spin','arrow'), true)) $out['burger_anim'] = $d['burger_anim'];
  if(!in_array($out['back_position'], array('header','footer','replace_close'), true)) $out['back_position'] = $d['back_position'];
  $out['show_close'] = (!empty($out['show_close']) && $out['show_close']!=='0') ? '1' : '0';
  $out['socials_on'] = (!empty($out['socials_on']) && $out['socials_on']!=='0') ? '1' : '0';
  $out['credits_on'] = (!empty($out['credits_on']) && $out['credits_on']!=='0') ? '1' : '0';

  return $out;
}

add_action('admin_menu', function(){
  add_menu_page('ADREMM Mobile Menu — Instellingen','ADREMM Mobile Menu','manage_options',ADREMM_MMP_SLUG,'adremm_mmp_render_admin','dashicons-menu',59);
}, 9);

function adremm_mmp_menu_select_html($cur){
  $out  = '<select name="'.esc_attr(ADREMM_MMP_OPT).'[menu_src]" class="regular-text">';
  $out .= '<option value="auto" '.selected($cur,'auto',false).'>Automatisch (primary → eerste menu)</option>';

  $locs = get_registered_nav_menus();
  if($locs){
    $out .= '<optgroup label="Theme locations">';
    foreach($locs as $slug=>$desc){
      $val='location:'.$slug;
      $out .= '<option value="'.esc_attr($val).'" '.selected($cur,$val,false).'>'.esc_html($slug.' — '.$desc).'</option>';
    }
    $out .= '</optgroup>';
  }

  $menus = wp_get_nav_menus();
  if($menus){
    $out .= '<optgroup label="Specifieke menu’s">';
    foreach($menus as $m){
      $val='menu:'.$m->term_id;
      $out .= '<option value="'.esc_attr($val).'" '.selected($cur,$val,false).'>'.esc_html($m->name.' (ID '.$m->term_id.')').'</option>';
    }
    $out .= '</optgroup>';
  }

  $out .= '</select>';
  return $out;
}

function adremm_mmp_divi_guess_nav_font(){
  if(function_exists('et_get_option')){
    $nav = et_get_option('primary_nav_font', '');
    $body= et_get_option('body_font', '');
    if($nav) return $nav;
    if($body) return $body;
  }
  return '';
}

function adremm_mmp_collect_google_families($o){
  $f = array();
  if(isset($o['layers']) && is_array($o['layers'])){
    foreach($o['layers'] as $layer){
      if(($layer['mode'] ?? '') === 'google' && !empty($layer['family'])){
        $f[] = $layer['family'];
      }
    }
  }
  $f = array_values(array_unique(array_filter(array_map('trim',$f))));
  return $f;
}

function adremm_mmp_render_layer_row($key, $title, $layer){
  $modes = array('theme'=>'Thema','google'=>'Google','custom'=>'Custom');
  $units = adremm_mmp_units();
  $ul = array('none'=>'Geen','solid'=>'Solid','dashed'=>'Dashed','dotted'=>'Dotted','double'=>'Double');
  ?>
  <div class="adremm-layer">
    <h3><?php echo esc_html($title); ?></h3>

    <div class="adremm-row">
      <label>Font modus</label>
      <select class="adremm-mode" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][mode]">
        <?php foreach($modes as $k=>$v): ?>
          <option value="<?php echo esc_attr($k); ?>" <?php selected($layer['mode'],$k); ?>><?php echo esc_html($v); ?></option>
        <?php endforeach; ?>
      </select>

      <label>Family</label>
      <select class="adremm-google" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][family]" <?php echo ($layer['mode']==='google')?'':'disabled'; ?>>
        <?php foreach(adremm_mmp_google_fonts() as $f): ?>
          <option value="<?php echo esc_attr($f); ?>" <?php selected($layer['family'],$f); ?>><?php echo esc_html($f); ?></option>
        <?php endforeach; ?>
      </select>
      <input class="adremm-custom" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][family]" value="<?php echo esc_attr($layer['family']); ?>" <?php echo ($layer['mode']==='custom')?'':'disabled'; ?> placeholder="bijv: Outfit, system-ui, sans-serif">
    </div>

    <div class="adremm-row">
      <label>Grootte</label>
      <input class="adremm-num" type="number" min="8" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][size]" value="<?php echo esc_attr($layer['size']); ?>">
      <select class="adremm-unit" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][unit]">
        <?php foreach($units as $u=>$t): ?><option value="<?php echo esc_attr($u); ?>" <?php selected($layer['unit'],$u); ?>><?php echo esc_html($t); ?></option><?php endforeach; ?>
      </select>

      <label>Gewicht</label>
      <input class="adremm-num" type="number" min="100" step="100" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][weight]" value="<?php echo esc_attr($layer['weight']); ?>">

      <label>Line</label>
      <input class="adremm-num" type="number" min="0.8" step="0.1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][line]" value="<?php echo esc_attr($layer['line']); ?>">
    </div>

    <div class="adremm-row">
      <label>Kleur</label>
      <input class="adremm-color adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][color]" value="<?php echo esc_attr($layer['color']); ?>">
      <label>Hover</label>
      <input class="adremm-color adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][hover]" value="<?php echo esc_attr($layer['hover']); ?>" placeholder="leeg = kleur">
      <label>Click</label>
      <input class="adremm-color adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][active]" value="<?php echo esc_attr($layer['active']); ?>" placeholder="leeg = kleur">
    </div>

    <div class="adremm-row">
      <label>Padding Y</label>
      <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][pad_y]" value="<?php echo esc_attr($layer['pad_y']); ?>"> px

      <label>Padding X</label>
      <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][pad_x]" value="<?php echo esc_attr($layer['pad_x']); ?>"> px

      <label>Gap</label>
      <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][gap]" value="<?php echo esc_attr($layer['gap']); ?>"> px
    </div>

    <div class="adremm-row">
      <label>Underline</label>
      <select class="adremm-ul" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][underline]">
        <?php foreach($ul as $u=>$t): ?><option value="<?php echo esc_attr($u); ?>" <?php selected($layer['underline'],$u); ?>><?php echo esc_html($t); ?></option><?php endforeach; ?>
      </select>

      <label>Dikte</label>
      <input class="adremm-num" type="number" min="1" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][ul_thick]" value="<?php echo esc_attr($layer['ul_thick']); ?>"> px

      <label>Afstand</label>
      <input class="adremm-num" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[layers][<?php echo esc_attr($key); ?>][ul_offset]" value="<?php echo esc_attr($layer['ul_offset']); ?>"> px
    </div>
  </div>
  <?php
}

function adremm_mmp_render_admin(){
  if ( ! current_user_can('manage_options') ) return;
  $o = adremm_mmp_get_options();
  $units = adremm_mmp_units();
  ?>
  <div class="wrap adremm-mmp-wrap">
    <h1>ADREMM Mobile Menu — Instellingen</h1>

    <?php if ( isset($_GET['activated']) ): ?>
      <div class="notice notice-success is-dismissible adremm-fade"><p>Plugin geactiveerd.</p></div>
    <?php endif; ?>

    <?php if ( isset($_GET['settings-updated']) && $_GET['settings-updated'] ): ?>
      <div class="notice notice-success is-dismissible adremm-fade"><p>Opgeslagen!</p></div>
    <?php endif; ?>

    <form method="post" action="options.php">
      <?php settings_fields(ADREMM_MMP_OPT); ?>
      <?php settings_errors(ADREMM_MMP_OPT); ?>

      <div class="adremm-tabs">
        <button type="button" class="adremm-tab is-active" data-tab="general">Algemeen</button>
        <button type="button" class="adremm-tab" data-tab="behavior">Gedrag</button>
        <button type="button" class="adremm-tab" data-tab="typography">Typografie</button>
        <button type="button" class="adremm-tab" data-tab="socials">Socials</button>
        <button type="button" class="adremm-tab" data-tab="credits">Credits</button>
        <button type="button" class="adremm-tab" data-tab="colors">Kleuren</button>
      </div>

      <div class="adremm-panel is-active" data-panel="general">
        <h2>Algemene instellingen</h2>
        <table class="form-table adremm-table" role="presentation">
          <tbody>
            <tr><th><label>Menu kiezen</label></th><td><?php echo adremm_mmp_menu_select_html($o['menu_src']); ?></td></tr>
            <tr><th><label>Menu label</label></th><td><input class="regular-text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[label]" value="<?php echo esc_attr($o['label']); ?>"></td></tr>
            <tr><th><label>Toggle uitlijning</label></th><td>
              <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_align]">
                <option value="left" <?php selected($o['toggle_align'],'left'); ?>>Links</option>
                <option value="center" <?php selected($o['toggle_align'],'center'); ?>>Midden</option>
                <option value="right" <?php selected($o['toggle_align'],'right'); ?>>Rechts</option>
              </select>
              <label class="adremm-inline">Fixed</label>
              <select class="adremm-w90" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_fixed]">
                <option value="0" <?php selected($o['toggle_fixed'],'0'); ?>>Nee</option>
                <option value="1" <?php selected($o['toggle_fixed'],'1'); ?>>Ja</option>
              </select>
              <label class="adremm-inline">Top</label><input class="adremm-w90" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_top]" value="<?php echo esc_attr($o['toggle_top']); ?>"><?php echo adremm_mmp_unit_select('toggle_top_unit', $o['toggle_top_unit'], $units); ?>
              <label class="adremm-inline">Zij</label><input class="adremm-w90" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_side]" value="<?php echo esc_attr($o['toggle_side']); ?>"><?php echo adremm_mmp_unit_select('toggle_side_unit', $o['toggle_side_unit'], $units); ?>
            </td></tr>
            <tr><th><label>Toggle achtergrond</label></th><td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[toggle_bg]" value="<?php echo esc_attr($o['toggle_bg']); ?>"></td></tr>
            <tr><th><label>Breakpoint</label></th><td><input class="adremm-w150" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[breakpoint]" value="<?php echo esc_attr($o['breakpoint']); ?>"><?php echo adremm_mmp_unit_select('breakpoint_unit', $o['breakpoint_unit'], $units); ?></td></tr>
            <tr><th><label>Z-index</label></th><td><input class="adremm-w150" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[z_index]" value="<?php echo esc_attr($o['z_index']); ?>"></td></tr>
            <tr><th><label>Google Fonts API Key</label></th><td><input class="regular-text" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[google_fonts_api_key]" value="<?php echo esc_attr($o['google_fonts_api_key']); ?>" placeholder="AIza..."><p class="description">Vul hier je Google Fonts API key in voor de actuele fontlijst. <a href="https://console.cloud.google.com/apis/library/webfonts.googleapis.com" target="_blank">API activeren</a></p></td></tr>
          </tbody>
        </table>
      </div>

      <div class="adremm-panel" data-panel="behavior">
        <h2>Gedrag</h2>
        <table class="form-table adremm-table" role="presentation">
          <tbody>
            <tr><th><label>Drawer</label></th><td><select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[drawer]"><option value="left" <?php selected($o['drawer'],'left'); ?>>Left</option><option value="right" <?php selected($o['drawer'],'right'); ?>>Right</option><option value="fullscreen" <?php selected($o['drawer'],'fullscreen'); ?>>Fullscreen</option></select></td></tr>
            <tr><th><label>Easing</label></th><td><select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[easing]"><option value="ease" <?php selected($o['easing'],'ease'); ?>>ease</option><option value="ease-in" <?php selected($o['easing'],'ease-in'); ?>>ease-in</option><option value="ease-out" <?php selected($o['easing'],'ease-out'); ?>>ease-out</option><option value="ease-in-out" <?php selected($o['easing'],'ease-in-out'); ?>>ease-in-out</option><option value="linear" <?php selected($o['easing'],'linear'); ?>>linear</option></select></td></tr>
            <tr><th><label>Hamburger animatie</label></th><td><select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_anim]"><option value="swap" <?php selected($o['burger_anim'],'swap'); ?>>Swap (kruis)</option><option value="squeeze" <?php selected($o['burger_anim'],'squeeze'); ?>>Squeeze</option><option value="morph" <?php selected($o['burger_anim'],'morph'); ?>>Morph</option><option value="spin" <?php selected($o['burger_anim'],'spin'); ?>>Spin</option><option value="arrow" <?php selected($o['burger_anim'],'arrow'); ?>>Arrow</option></select></td></tr>

            <tr>
              <th><label>Submenu indicator</label></th>
              <td>
                <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon]">
                  <option value="chevron" <?php selected($o['submenu_icon'],'chevron'); ?>>Chevron</option>
                  <option value="caret" <?php selected($o['submenu_icon'],'caret'); ?>>Caret</option>
                  <option value="plus" <?php selected($o['submenu_icon'],'plus'); ?>>Plus</option>
                  <option value="custom" <?php selected($o['submenu_icon'],'custom'); ?>>Eigen (URL)</option>
                </select>
                <label class="adremm-inline">URL</label>
                <input id="adremm_submenu_icon_url" class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon_url]" value="<?php echo esc_attr($o['submenu_icon_url']); ?>" placeholder="https://.../icon.svg">
                <button type="button" class="button adremm-inline adremm-media-btn" data-target="adremm_submenu_icon_url">Kies</button>
                <label class="adremm-inline">Size</label><input class="adremm-w90" type="number" min="6" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[submenu_icon_size]" value="<?php echo esc_attr($o['submenu_icon_size']); ?>"><?php echo adremm_mmp_unit_select('submenu_icon_unit', $o['submenu_icon_unit'], $units); ?>
              </td>
            </tr>

            <tr>
              <th><label>Terug knop</label></th>
              <td>
                <input class="adremm-w150" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_label]" value="<?php echo esc_attr($o['back_label']); ?>">
                <label class="adremm-inline">Positie</label>
                <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_position]">
                  <option value="header" <?php selected($o['back_position'],'header'); ?>>Header</option>
                  <option value="footer" <?php selected($o['back_position'],'footer'); ?>>Footer</option>
                  <option value="replace_close" <?php selected($o['back_position'],'replace_close'); ?>>I.p.v. sluit</option>
                </select>
                <label class="adremm-inline">Icon</label>
                <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon]">
                  <option value="chevron" <?php selected($o['back_icon'],'chevron'); ?>>Chevron</option>
                  <option value="caret" <?php selected($o['back_icon'],'caret'); ?>>Caret</option>
                  <option value="plus" <?php selected($o['back_icon'],'plus'); ?>>Plus</option>
                  <option value="custom" <?php selected($o['back_icon'],'custom'); ?>>Eigen (URL)</option>
                </select>
                <label class="adremm-inline">URL</label>
                <input id="adremm_back_icon_url" class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon_url]" value="<?php echo esc_attr($o['back_icon_url']); ?>" placeholder="https://.../icon.svg">
                <button type="button" class="button adremm-inline adremm-media-btn" data-target="adremm_back_icon_url">Kies</button>
                <label class="adremm-inline">Size</label><input class="adremm-w90" type="number" min="6" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[back_icon_size]" value="<?php echo esc_attr($o['back_icon_size']); ?>"><?php echo adremm_mmp_unit_select('back_icon_unit', $o['back_icon_unit'], $units); ?>
              </td>
            </tr>

            <tr>
              <th><label>Sluitkruis</label></th>
              <td>
                <label class="adremm-inline">Tonen</label>
                <select class="adremm-w90" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[show_close]"><option value="1" <?php selected($o['show_close'],'1'); ?>>Ja</option><option value="0" <?php selected($o['show_close'],'0'); ?>>Nee</option></select>
                <label class="adremm-inline">Size</label><input class="adremm-w90" type="number" min="10" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[close_size]" value="<?php echo esc_attr($o['close_size']); ?>"><?php echo adremm_mmp_unit_select('close_unit', $o['close_unit'], $units); ?>
                <label class="adremm-inline">Kleur</label><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[close_color]" value="<?php echo esc_attr($o['close_color']); ?>">
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="adremm-panel" data-panel="typography">
        <div class="adremm-row" style="margin-bottom:14px;">
          <label>Globaal font</label>
          <input class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[global_font]" value="<?php echo esc_attr($o['global_font']); ?>" placeholder="inherit of bv. geom-graphic, sans-serif">
          <span class="adremm-help-inline">Basis font-family voor het menu. Per-laag op 'Thema' gebruikt dit als fallback.</span>
        </div>

        <h2>Typografie per laag</h2>
        <?php
          adremm_mmp_render_layer_row('menu_label','Menu label',$o['layers']['menu_label']);
          adremm_mmp_render_layer_row('items','Navigatie items',$o['layers']['items']);
          adremm_mmp_render_layer_row('subitems','Subnav',$o['layers']['subitems']);
          adremm_mmp_render_layer_row('back','Terug knop',$o['layers']['back']);
        ?>
      </div>

      <div class="adremm-panel" data-panel="socials">
        <h2>Socials</h2>
        <table class="form-table adremm-table" role="presentation">
          <tbody>
            <tr>
              <th><label>Socials</label></th>
              <td>
                <select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials_on]"><option value="0" <?php selected($o['socials_on'],'0'); ?>>Uit</option><option value="1" <?php selected($o['socials_on'],'1'); ?>>Aan</option></select>
                <label class="adremm-inline">Icon size</label><input class="adremm-w90" type="number" min="12" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials_size]" value="<?php echo esc_attr($o['socials_size']); ?>"><?php echo adremm_mmp_unit_select('socials_unit', $o['socials_unit'], $units); ?>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="adremm-social-grid">
          <?php foreach(adremm_mmp_social_platforms() as $k=>$p): $row = $o['socials'][$k] ?? array('on'=>'0','url'=>'','custom_icon_url'=>''); ?>
            <div class="adremm-social-row">
              <label class="adremm-check"><input type="checkbox" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][on]" value="1" <?php checked($row['on'],'1'); ?>><?php echo esc_html($p['label']); ?></label>
              <input class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][url]" value="<?php echo esc_attr($row['url']); ?>" placeholder="https://...">
              <input id="adremm_social_<?php echo esc_attr($k); ?>_icon" class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[socials][<?php echo esc_attr($k); ?>][custom_icon_url]" value="<?php echo esc_attr($row['custom_icon_url']); ?>" placeholder="Custom icon (URL)">
              <button type="button" class="button adremm-media-btn" data-target="adremm_social_<?php echo esc_attr($k); ?>_icon">Kies</button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="adremm-panel" data-panel="credits">
        <h2>Credits</h2>
        <table class="form-table adremm-table" role="presentation">
          <tbody>
            <tr><th><label>Credits</label></th><td><select class="adremm-w150" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_on]"><option value="0" <?php selected($o['credits_on'],'0'); ?>>Uit</option><option value="1" <?php selected($o['credits_on'],'1'); ?>>Aan</option></select></td></tr>
            <tr><th><label>Tekst</label></th><td><input class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_text]" value="<?php echo esc_attr($o['credits_text']); ?>"></td></tr>
            <tr><th><label>URL</label></th><td><input class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_url]" value="<?php echo esc_attr($o['credits_url']); ?>"></td></tr>
            <tr><th><label>Icon URL</label></th><td><input id="adremm_credits_icon_url" class="adremm-w250" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_icon_url]" value="<?php echo esc_attr($o['credits_icon_url']); ?>" placeholder="https://.../icon.svg (optioneel)"><button type="button" class="button adremm-inline adremm-media-btn" data-target="adremm_credits_icon_url">Kies</button><label class="adremm-inline">Size</label><input class="adremm-w90" type="number" min="12" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[credits_size]" value="<?php echo esc_attr($o['credits_size']); ?>"><?php echo adremm_mmp_unit_select('credits_unit', $o['credits_unit'], $units); ?></td></tr>
          </tbody>
        </table>
      </div>

      <div class="adremm-panel" data-panel="colors">
        <h2>Kleuren</h2>
        <table class="form-table adremm-table" role="presentation">
          <tbody>
            <tr><th><label>Panel achtergrond</label></th><td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_bg]" value="<?php echo esc_attr($o['panel_bg']); ?>"></td></tr>
            <tr><th><label>Panel randkleur</label></th><td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_border]" value="<?php echo esc_attr($o['panel_border']); ?>"></td></tr>
            <tr><th><label>Randbreedte (px)</label></th><td><input class="adremm-w90" type="number" min="0" step="1" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[panel_border_width]" value="<?php echo esc_attr($o['panel_border_width']); ?>"> px (0 = geen rand)</td></tr>
            <tr><th><label>Overlay</label></th><td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[overlay]" value="<?php echo esc_attr($o['overlay']); ?>"></td></tr>
            <tr><th><label>Burger kleur dicht</label></th><td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_off]" value="<?php echo esc_attr($o['burger_off']); ?>"></td></tr>
            <tr><th><label>Burger kleur open</label></th><td><input class="adremm-w250 adremm-rgba" data-rgba="1" type="text" name="<?php echo esc_attr(ADREMM_MMP_OPT); ?>[burger_on]" value="<?php echo esc_attr($o['burger_on']); ?>"></td></tr>
          </tbody>
        </table>
      </div>

      <?php submit_button('Wijzigingen opslaan'); ?>
    </form>

    <div class="adremm-footer">
      <span>ADREMM Mobile Menu PRO v<?php echo esc_html(ADREMM_MMP_VERSION); ?></span>
      <a class="button button-secondary adremm-support" href="https://adremm.nl" target="_blank" rel="noopener noreferrer">Support</a>
      <span><?php echo esc_html(date('Y')); ?> ADREMM</span>
    </div>
  </div>
  <?php
}

add_action('admin_enqueue_scripts', function(){
  if ( isset($_GET['page']) && $_GET['page'] === ADREMM_MMP_SLUG ) {
    wp_enqueue_media();
    $css = "
.adremm-fade{animation:adremmfade 3s forwards}
@keyframes adremmfade{0%,80%{opacity:1}100%{opacity:0}}
.adremm-mmp-wrap .adremm-tabs{display:flex;gap:8px;margin:12px 0 10px 0;flex-wrap:wrap}
.adremm-mmp-wrap .adremm-tab{border:1px solid #ccd0d4;background:#fff;padding:8px 10px;cursor:pointer}
.adremm-mmp-wrap .adremm-tab.is-active{background:#111;color:#fff;border-color:#111}
.adremm-mmp-wrap .adremm-panel{display:none;border:1px solid #ccd0d4;background:#fff;padding:12px;margin-bottom:14px}
.adremm-mmp-wrap .adremm-panel.is-active{display:block}
.adremm-table th{width:220px}
.adremm-inline{margin-left:10px;margin-right:8px;display:inline-block}
.adremm-w90{width:90px}
.adremm-w150{width:150px}
.adremm-w250{width:250px}
.adremm-unit{width:70px;margin-left:6px}
.adremm-layer{border-top:1px solid #e5e5e5;padding-top:10px;margin-top:10px}
.adremm-row{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:8px 0}
.adremm-row label{width:150px}
.adremm-row select,.adremm-row input{height:34px}
.adremm-num{width:150px}
.adremm-color{width:250px}
.adremm-google,.adremm-custom{width:250px}
.adremm-social-grid{display:grid;grid-template-columns:1fr;gap:8px}
.adremm-social-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap;border:1px solid #e5e5e5;padding:8px}
.adremm-check{min-width:160px}
.adremm-footer{display:flex;gap:12px;align-items:center;margin-top:12px}
.adremm-support{background:#222!important;color:#fff!important;border-color:#222!important}


/* RGBA inputs */
input.adremm-rgba{max-width:150px;font-weight:700}
.adremm-swatch{width:28px;height:28px;border-radius:6px;border:1px solid #ccd0d4;display:inline-block;vertical-align:middle;cursor:pointer}
/* RGBA picker popover */
.adremm-rgba-pop{position:absolute;z-index:999999;background:#fff;border:1px solid #ccd0d4;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,.14);padding:12px;min-width:360px;display:none}
.adremm-rgba-top{display:flex;gap:12px;align-items:flex-start}
.adremm-rgba-preview{width:56px;height:56px;border-radius:10px;border:1px solid #ccd0d4;background:#111}
.adremm-rgba-controls{flex:1;display:grid;grid-template-columns:70px 1fr;gap:8px 10px;align-items:center}
.adremm-rgba-lbl{font-weight:600}
.adremm-rgba-color{width:56px;height:34px;padding:0;border:1px solid #ccd0d4;border-radius:8px;background:#fff}
.adremm-rgba-hex{width:100%;max-width:220px}
.adremm-rgba-alpha{grid-column:1 / -1;display:flex;align-items:center;gap:10px}
.adremm-rgba-a{flex:1}
.adremm-rgba-aout{min-width:46px;text-align:right;font-variant-numeric:tabular-nums}
.adremm-rgba-actions{grid-column:1 / -1;display:flex;justify-content:flex-end;margin-top:6px}
.adremm-rgba-inline{width:34px;height:34px;border-radius:8px;border:1px solid #ccd0d4;display:inline-block;vertical-align:middle;cursor:pointer;margin-left:8px}
";
    wp_register_style('adremm-mmp-admin', false, array(), ADREMM_MMP_VERSION);
    wp_enqueue_style('adremm-mmp-admin');
    wp_add_inline_style('adremm-mmp-admin', $css);

    $js = <<<'ADREMM_JS'
(function(){
  function q(sel, ctx){ return Array.prototype.slice.call((ctx||document).querySelectorAll(sel)); }
  function setActiveTab(key){
    q('.adremm-tab').forEach(function(b){ b.classList.toggle('is-active', (b.getAttribute('data-tab')===key)); });
    q('.adremm-panel').forEach(function(p){ p.classList.toggle('is-active', (p.getAttribute('data-panel')===key)); });
  }

  function wireTabs(){
    q('.adremm-tab').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var key = btn.getAttribute('data-tab') || 'general';
        setActiveTab(key);
      });
    });
  }

  function wireLayerModes(){
    q('.adremm-layer').forEach(function(layer){
      var modeSel = layer.querySelector('.adremm-mode');
      var google  = layer.querySelector('.adremm-google');
      var custom  = layer.querySelector('.adremm-custom');
      function sync(){
        var mode = modeSel ? modeSel.value : 'theme';
        if(google) google.disabled = (mode !== 'google');
        if(custom) custom.disabled = (mode !== 'custom');
      }
      if(modeSel){ modeSel.addEventListener('change', sync); }
      sync();
    });
  }

  function wireMediaButtons(){
    document.addEventListener('click', function(e){
      var btn = e.target.closest ? e.target.closest('.adremm-media, .adremm-media-btn') : null;
      if(!btn) return;
      e.preventDefault();
      e.stopPropagation();
      if(typeof wp === 'undefined' || !wp.media) return;

      var target = btn.getAttribute('data-target');
      if(!target) return;
      var input = document.getElementById(target) || (target ? document.querySelector(target) : null);
      if(!input) return;

      var frame = wp.media({ title: 'Kies media', button: { text: 'Gebruik deze' }, multiple: false });
      frame.on('select', function(){
        var att = frame.state().get('selection').first();
        if(att && att.toJSON && att.toJSON().url){
          input.value = att.toJSON().url;
          input.dispatchEvent(new Event('change', {bubbles:true}));
        }
      });
      frame.open();
    }, true);
  }


  function parseRGBA(str){
    str = String(str||'').trim();
    var m = str.match(/^rgba?\((\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*,\s*([0-9.]+))?\)$/i);
    if(m){ return {r:+m[1],g:+m[2],b:+m[3],a:(m[4]!==undefined?Math.max(0,Math.min(1,parseFloat(m[4]))):1)}; }
    // hex
    var hx = str.replace('#','');
    if(/^[0-9a-f]{6}$/i.test(hx)){
      return {r:parseInt(hx.substr(0,2),16),g:parseInt(hx.substr(2,2),16),b:parseInt(hx.substr(4,2),16),a:1};
    }
    return {r:17,g:17,b:17,a:1};
  }
  function toRGBA(c){ return 'rgba('+c.r+','+c.g+','+c.b+','+(Math.round(c.a*100)/100)+')'; }

  function makePopover(){
    var pop = document.createElement('div');
    pop.className = 'adremm-rgba-pop';
    pop.innerHTML =
      '<div class="adremm-rgba-top">'
      + '  <div class="adremm-rgba-preview" title="Preview"></div><input class="adremm-native-color" type="color" title="Kies kleur">'
      + '  <div class="adremm-rgba-controls">'
      + '    <label class="adremm-rgba-lbl">Kleur</label><input class="adremm-rgba-color" type="color" value="#111111">'
      + '    <label class="adremm-rgba-lbl">Hex</label><input class="adremm-rgba-hex" type="text" inputmode="text" maxlength="7" placeholder="#RRGGBB">'
      + '    <div class="adremm-rgba-alpha">'
      + '      <label class="adremm-rgba-lbl">Alpha</label>'
      + '      <input class="adremm-rgba-a" type="range" min="0" max="1" step="0.01" value="1">'
      + '      <span class="adremm-rgba-aout">1.00</span>'
      + '    </div>'
      + '    <div class="adremm-rgba-actions">'
      + '      <button type="button" class="button button-primary adremm-rgba-ok">OK</button>'
      + '    </div>'
      + '  </div>'
      + '</div>';
    document.body.appendChild(pop);
    return pop;
  }


  function wireRGBA(){
    var pop = null, current = null, color = null;

    function rgbToHex(r,g,b){
      function h(n){ n = Math.max(0, Math.min(255, parseInt(n,10)||0)); var s=n.toString(16); return (s.length===1?'0':'')+s; }
      return '#'+h(r)+h(g)+h(b);
    }

    function setPreview(){
      if(!pop || !color) return;
      var rgba = toRGBA(color);
      pop.querySelector('.adremm-rgba-preview').style.background = rgba;
      pop.querySelector('.adremm-rgba-aout').textContent = (Math.round(color.a*100)/100).toFixed(2);
    }

    function close(){
      if(pop) pop.style.display='none';
      current = null;
      color = null;
    }

    function openFor(inp){
      current = inp;
      color = parseRGBA(inp.value);

      if(!pop) pop = makePopover();

      var rect = inp.getBoundingClientRect();
      pop.style.display='block';
      pop.style.top = (window.scrollY + rect.bottom + 8) + 'px';
      pop.style.left = (window.scrollX + rect.left) + 'px';

      var hex = rgbToHex(color.r, color.g, color.b);
      pop.querySelector('.adremm-rgba-color').value = hex;
      pop.querySelector('.adremm-rgba-hex').value = hex;
      pop.querySelector('.adremm-rgba-a').value = String(color.a);
      setPreview();
    }

    function commit(){
      if(!current || !color) return;
      current.value = toRGBA(color);
      try { current.dispatchEvent(new Event('change', {bubbles:true})); } catch(e){}
    }

    // Open on click (1x)
    q('input.adremm-rgba').forEach(function(inp){
      inp.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        openFor(inp);
      });
    });

    // Swatch next to input: click opens picker; swatch reflects current value
    q('input.adremm-rgba').forEach(function(inp){
      var sw = inp.parentElement ? inp.parentElement.querySelector('.adremm-swatch') : null;
      if(sw){
        try{ sw.style.background = toRGBA(parseRGBA(inp.value)); }catch(e){}
        sw.addEventListener('click', function(e){
          e.preventDefault(); e.stopPropagation();
          openFor(inp);
        });
        inp.addEventListener('input', function(){
          try{ sw.style.background = toRGBA(parseRGBA(inp.value)); }catch(e){}
        });
        inp.addEventListener('change', function(){
          try{ sw.style.background = toRGBA(parseRGBA(inp.value)); }catch(e){}
        });
      }
    });


    // Popover events
    document.addEventListener('click', function(e){
      if(!pop || pop.style.display!=='block') return;
      if(e.target && (pop.contains(e.target) || e.target.classList.contains('adremm-rgba'))) return;
      close();
    }, true);

    document.addEventListener('keydown', function(e){
      if(e.key==='Escape') close();
    });

    document.addEventListener('input', function(e){
      if(!pop || pop.style.display!=='block') return;

      if(e.target.classList.contains('adremm-rgba-color')){
        var hx = e.target.value || '#111111';
        var c = parseRGBA(hx);
        color.r=c.r; color.g=c.g; color.b=c.b;
        pop.querySelector('.adremm-rgba-hex').value = rgbToHex(color.r,color.g,color.b);
        setPreview();
      }

if(e.target.classList.contains('adremm-native-color')){
        var hx = e.target.value || '#111111';
        var c = parseRGBA(hx);
        color.r=c.r; color.g=c.g; color.b=c.b; color.a=1;
        pop.querySelector('.adremm-rgba-color').value = rgbToHex(color.r,color.g,color.b);
        pop.querySelector('.adremm-rgba-hex').value = rgbToHex(color.r,color.g,color.b);
        setPreview();
        var ok = pop.querySelector('.adremm-rgba-ok'); if(ok) ok.click();
      }

      if(e.target.classList.contains('adremm-rgba-hex')){
        var v = (e.target.value||'').trim();
        if(/^#?[0-9a-fA-F]{6}$/.test(v)){
          var c2 = parseRGBA(v);
          color.r=c2.r; color.g=c2.g; color.b=c2.b;
          var hx2 = rgbToHex(color.r,color.g,color.b);
          pop.querySelector('.adremm-rgba-color').value = hx2;
          setPreview();
        }
      }

      if(e.target.classList.contains('adremm-rgba-a')){
        color.a = Math.max(0, Math.min(1, parseFloat(e.target.value)||0));
        setPreview();
      }
    });

    document.addEventListener('click', function(e){
      if(!pop || pop.style.display!=='block') return;
      if(e.target.classList.contains('adremm-rgba-ok')){
        commit();
        close();
      }
    });
  }



  function wireUploadRedirect(){
    try{
      var qs = new URLSearchParams(window.location.search||'');
      if(qs.get('action')!=='upload-plugin') return;
      if(qs.get('overwrite')!=='update-plugin') return;
      // Only on WP "Plugin updated successfully." screen
      if(!/Plugin updated successfully/i.test(document.body.textContent||'')) return;
      // Redirect to settings page (preference A)
      window.setTimeout(function(){
        window.location.href = (window.ajaxurl ? (window.location.origin + window.location.pathname.replace(/\/wp-admin\/.*/, '/wp-admin/') ) : '') + 'options-general.php?page=adremm-mm-pro';
      }, 300);
    }catch(e){}
  }


  function wireNotices(){
    try{
      var n = document.querySelector('.adremm-notice');
      if(!n) return;
      window.setTimeout(function(){
        n.style.transition='opacity .25s ease';
        n.style.opacity='0';
        window.setTimeout(function(){ if(n && n.parentNode) n.parentNode.removeChild(n); }, 260);
      }, 3000);
    }catch(e){}
  }

function boot(){
    wireTabs();
    wireLayerModes();
    wireMediaButtons();
    wireRGBA();
    wireUploadRedirect();
    wireNotices();
  }

  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', boot); }
  else { boot(); }
})();
ADREMM_JS;

    wp_register_script('adremm-mmp-admin', '', array('jquery'), ADREMM_MMP_VERSION, true);
    wp_enqueue_script('adremm-mmp-admin');
    wp_add_inline_script('adremm-mmp-admin', $js);
  }
});

function adremm_mmp_resolve_menu_args($src){
  if (strpos($src, 'location:') === 0) return array('theme_location' => substr($src, 9), 'container' => false, 'fallback_cb' => false, 'echo' => false);
  if (strpos($src, 'menu:') === 0) return array('menu' => intval(substr($src, 5)), 'container' => false, 'fallback_cb' => false, 'echo' => false);

  $menus = get_nav_menu_locations();
  if (isset($menus['primary'])) return array('theme_location' => 'primary','container'=>false,'fallback_cb'=>false,'echo'=>false);

  $first = wp_get_nav_menus();
  if ($first && !empty($first)) return array('menu' => $first[0]->term_id,'container'=>false,'fallback_cb'=>false,'echo'=>false);

  return array('echo'=>false,'fallback_cb'=>false,'container'=>false);
}

function adremm_mmp_layer_css_vars($layer, $prefix, $theme_font){
  $mode = $layer['mode'] ?? 'theme';
  $family = $layer['family'] ?? 'inherit';

  // Theme mode should truly inherit from the site/theme, not guess.
  if($mode==='theme') $family = 'var(--mm-global-ff, inherit)';
  elseif($mode==='google') $family = $family ? ($family.', system-ui, sans-serif') : 'inherit';
  else $family = $family ? $family : 'inherit';

  $color = $layer['color'] ?? 'rgba(17,17,17,1)';
  $hover = $layer['hover'] ?? '';
  $active= $layer['active'] ?? '';
  $hover = $hover ? $hover : $color;
  $active= $active ? $active : $color;

  return array(
    "--mm-{$prefix}-ff" => $family,
    "--mm-{$prefix}-fs" => ($layer['size'] ?? '16').($layer['unit'] ?? 'px'),
    "--mm-{$prefix}-fw" => ($layer['weight'] ?? '500'),
    "--mm-{$prefix}-lh" => ($layer['line'] ?? '1.2'),
    "--mm-{$prefix}-c"  => $color,
    "--mm-{$prefix}-ch" => $hover,
    "--mm-{$prefix}-ca" => $active,
    "--mm-{$prefix}-py" => ($layer['pad_y'] ?? '10').'px',
    "--mm-{$prefix}-px" => ($layer['pad_x'] ?? '12').'px',
    "--mm-{$prefix}-gap"=> ($layer['gap'] ?? '8').'px',
    "--mm-{$prefix}-ul" => ($layer['underline'] ?? 'none'),
    "--mm-{$prefix}-ult"=> ($layer['ul_thick'] ?? '2').'px',
    "--mm-{$prefix}-ulo"=> ($layer['ul_offset'] ?? '3').'px'
  );
}

function adremm_mmp_style_attr($vars){
  $s='';
  foreach($vars as $k=>$v) $s .= $k.':'.esc_attr($v).';';
  return $s;
}

function adremm_mmp_shortcode($atts){
  $o = adremm_mmp_get_options();
  $units = adremm_mmp_units();
  $a = shortcode_atts(array(
    'label'  => $o['label'],
    'drawer' => $o['drawer'],
    'easing' => $o['easing'],
    'z'      => $o['z_index'],
  ), $atts, 'adremm_mobile_menu');

  $args = adremm_mmp_resolve_menu_args($o['menu_src']);
  $menu_html = wp_nav_menu($args);
  if (!$menu_html) $menu_html = '<ul class="menu"><li><a href="#">(Nog geen menu ingesteld)</a></li></ul>';

  $theme_font = adremm_mmp_divi_guess_nav_font();

  $vars = array(
    '--mm-base-ff' => ($o['global_font'] ?? 'inherit'),
    '--mm-panel-bg' => $o['panel_bg'],
    '--mm-panel-border' => $o['panel_border'],
    '--mm-border-width' => $o['panel_border_width'].'px',
    '--mm-overlay' => $o['overlay'],
    '--mm-burger-off' => $o['burger_off'],
    '--mm-burger-on'  => $o['burger_on'],
    '--mm-ease' => $a['easing'],
    '--mm-z'    => $a['z'],
    '--mm-global-ff' => $o['global_font'],
    '--mm-ind-size' => $o['submenu_icon_size'].'px',
    '--mm-back-ind-size' => $o['back_icon_size'].'px',
    '--mm-anim' => $o['burger_anim'],
    '--mm-panel-w' => $o['panel_w'],
    '--mm-panel-max' => $o['panel_max'],
    '--mm-fs-nav-max' => $o['fs_nav_max'],
    '--mm-close-size' => $o['close_size'].'px',
    '--mm-close-color'=> $o['close_color'],
    '--mm-icon-url' => ($o['submenu_icon']==='custom' && $o['submenu_icon_url']) ? 'url('.esc_url($o['submenu_icon_url']).')' : 'none',
    '--mm-back-icon-url' => ($o['back_icon']==='custom' && $o['back_icon_url']) ? 'url('.esc_url($o['back_icon_url']).')' : 'none',
    '--mm-social-size' => $o['socials_size'].'px',
    '--mm-credits-size'=> $o['credits_size'].'px',
  );

  $vars = array_merge($vars, adremm_mmp_layer_css_vars($o['layers']['menu_label'], 'label', $theme_font));
  $vars = array_merge($vars, adremm_mmp_layer_css_vars($o['layers']['items'], 'item', $theme_font));
  $vars = array_merge($vars, adremm_mmp_layer_css_vars($o['layers']['subitems'], 'sub', $theme_font));
  $vars = array_merge($vars, adremm_mmp_layer_css_vars($o['layers']['back'], 'back', $theme_font));

  ob_start(); ?>
  <div class="adremm-mm adremm-mm--drawer-<?php echo esc_attr($a['drawer']); ?> adremm-mm--back-<?php echo esc_attr($o['back_position']); ?> adremm-mm--align-<?php echo esc_attr($o['toggle_align']); ?> <?php echo ($o['toggle_fixed']==='1')?'adremm-mm--fixed':''; ?>"
       data-subicon="<?php echo esc_attr($o['submenu_icon']); ?>"
       data-backicon="<?php echo esc_attr($o['back_icon']); ?>"
       data-showclose="<?php echo esc_attr($o['show_close']); ?>"
       data-socials="<?php echo esc_attr($o['socials_on']); ?>"
       data-credits="<?php echo esc_attr($o['credits_on']); ?>"
       style="<?php echo adremm_mmp_style_attr($vars); ?>">
    <div class="adremm-mm__toggle-wrap">
      <button class="adremm-mm__toggle" aria-expanded="false" type="button">
        <span class="adremm-mm__burger" aria-hidden="true"><i></i><i></i></span>
        <span class="adremm-mm__label"><?php echo esc_html($a['label']); ?></span>
      </button>
    </div>

    <div class="adremm-mm__overlay" hidden></div>

    <div class="adremm-mm__panel" hidden>
      <div class="adremm-mm__header">
        <button class="adremm-mm__back adremm-mm__back--header" type="button" hidden>
          <span class="adremm-mm__backicon" aria-hidden="true"></span><span class="adremm-mm__backtext"><?php echo esc_html($o['back_label']); ?></span>
        </button>
        <button class="adremm-mm__close" type="button" aria-label="Sluit menu"><span class="adremm-mm__cross" aria-hidden="true"></span></button>
      </div>

      <nav class="adremm-mm__nav">
        <div class="adremm-mm__views">
          <div class="adremm-mm__view is-root"><?php echo $menu_html; ?></div>
        </div>
      </nav>

      <div class="adremm-mm__bottom">
        <button class="adremm-mm__back adremm-mm__back--footer" type="button" hidden>
          <span class="adremm-mm__backicon" aria-hidden="true"></span><span class="adremm-mm__backtext"><?php echo esc_html($o['back_label']); ?></span>
        </button>
        <div class="adremm-mm__socials" hidden></div>
        <div class="adremm-mm__credits" hidden></div>
      </div>
    </div>
  </div>
  <?php return ob_get_clean();
}
add_shortcode('adremm_mobile_menu','adremm_mmp_shortcode');

/**
 * Conditioneel laden van frontend CSS/JS alleen als de shortcode op de pagina staat.
 */
add_action('wp', function() {
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'adremm_mobile_menu')) {
        add_action('wp_enqueue_scripts', 'adremm_mmp_enqueue_front', 20);
    }
});

function adremm_mmp_enqueue_front() {
  $o = adremm_mmp_get_options();

  // Google fonts used by any layer
  foreach(adremm_mmp_collect_google_families($o) as $fam){
    $slug = 'adremm-mmp-gf-'.sanitize_key($fam);
    $f = str_replace(' ', '+', trim($fam));
    wp_enqueue_style($slug, 'https://fonts.googleapis.com/css2?family='.rawurlencode($f).':wght@300;400;500;600;700&display=swap', array(), null);
  }

  $css = "
.adremm-mm{position:relative;z-index:var(--mm-z,999999)}
.adremm-mm *{box-sizing:border-box}
.adremm-mm ul,.adremm-mm li{list-style:none;margin:0;padding:0}
.adremm-mm a{text-decoration:none}
.adremm-mm__toggle-wrap{display:flex;justify-content:flex-end;position:relative;z-index:calc(var(--mm-z,999999) + 3)}
.adremm-mm--align-left .adremm-mm__toggle-wrap{justify-content:flex-start}
.adremm-mm--align-center .adremm-mm__toggle-wrap{justify-content:center}
.adremm-mm--fixed .adremm-mm__toggle-wrap{position:fixed;top:var(--mm-toggle-top,20px);right:var(--mm-toggle-side,20px);left:auto;z-index:calc(var(--mm-z,999999) + 50)}
.adremm-mm--fixed.adremm-mm--align-left .adremm-mm__toggle-wrap{left:var(--mm-toggle-side,20px);right:auto}
.adremm-mm--fixed.adremm-mm--align-center .adremm-mm__toggle-wrap{left:50%;right:auto;transform:translateX(-50%)}
.adremm-mm__toggle{display:inline-flex;align-items:center;gap:var(--mm-label-gap,10px);border:1px solid var(--mm-panel-border);background:var(--mm-toggle-bg);padding:var(--mm-label-py,10px) var(--mm-label-px,12px);cursor:pointer}
.adremm-mm__label{font-family:var(--mm-label-ff);font-size:var(--mm-label-fs);font-weight:var(--mm-label-fw);line-height:var(--mm-label-lh);color:var(--mm-label-c)}
.adremm-mm__burger{position:relative;width:22px;height:16px;display:inline-block}
.adremm-mm__burger i{position:absolute;left:0;right:0;height:2px;background:var(--mm-burger-off);transition:transform .28s var(--mm-ease), background .28s var(--mm-ease), top .28s var(--mm-ease), bottom .28s var(--mm-ease);display:block}
.adremm-mm__burger i:first-child{top:3px}
.adremm-mm__burger i:last-child{bottom:3px}

.adremm-mm__overlay{position:fixed;inset:0;background:var(--mm-overlay);opacity:0;transition:opacity .25s var(--mm-ease);z-index:calc(var(--mm-z,999999) + 1)}
.adremm-mm__panel{position:fixed;top:0;bottom:0;right:0;width:min(var(--mm-panel-w,90vw), var(--mm-panel-max,420px));background:var(--mm-toggle-bg);border-left:var(--mm-border-width,1px) solid var(--mm-panel-border);transform:translateX(100%);transition:transform .4s var(--mm-ease);z-index:calc(var(--mm-z,999999) + 2);display:grid;grid-template-rows:auto 1fr auto}
.adremm-mm--drawer-left .adremm-mm__panel{left:0;right:auto;border-left:none;border-right:var(--mm-border-width,1px) solid var(--mm-panel-border);transform:translateX(-100%)}
.adremm-mm--drawer-fullscreen .adremm-mm__panel{left:0;right:0;width:100vw;transform:translateY(-100%);border-left:none;border-right:none}
.adremm-mm--drawer-fullscreen .adremm-mm__view{display:flex;justify-content:center}
.adremm-mm--drawer-fullscreen .adremm-mm__view > ul{width:100%;max-width:var(--mm-fs-nav-max,520px)}
.adremm-mm__header{display:flex;align-items:center;gap:10px;justify-content:flex-end;padding:10px;border-bottom:1px solid var(--mm-panel-border)}
.adremm-mm__close{border:1px solid var(--mm-panel-border);background:transparent;padding:8px;cursor:pointer}
.adremm-mm__cross{width:var(--mm-close-size,16px);height:var(--mm-close-size,16px);position:relative;display:block}
.adremm-mm__cross:before,.adremm-mm__cross:after{content:'';position:absolute;left:0;right:0;top:50%;height:2px;background:var(--mm-close-color);transform-origin:center}
.adremm-mm__cross:before{transform:translateY(-50%) rotate(45deg)}
.adremm-mm__cross:after{transform:translateY(-50%) rotate(-45deg)}

.adremm-mm__nav{overflow:hidden;position:relative}
.adremm-mm__views{display:flex;flex-direction:row;width:100%;transition:transform .35s var(--mm-ease)}
.adremm-mm__view{min-width:100%;padding:8px 12px;overflow:auto}
.adremm-mm__view ul{display:flex;flex-direction:column;gap:6px}
.adremm-mm__view li.menu-item-has-children{display:flex;align-items:center;gap:10px;flex-wrap:nowrap}
.adremm-mm__view li.menu-item-has-children > a{flex:1;min-width:0;justify-content:flex-start}
.adremm-mm__view a{display:flex;justify-content:space-between;align-items:center;padding:var(--mm-item-py) var(--mm-item-px);border:1px solid var(--mm-panel-border);font-family:var(--mm-item-ff);font-size:var(--mm-item-fs);font-weight:var(--mm-item-fw);line-height:var(--mm-item-lh);color:var(--mm-item-c);position:relative}
.adremm-mm__view .sub-menu a{padding:var(--mm-sub-py) var(--mm-sub-px);font-family:var(--mm-sub-ff);font-size:var(--mm-sub-fs);font-weight:var(--mm-sub-fw);line-height:var(--mm-sub-lh);color:var(--mm-sub-c)}
.adremm-mm__view a:hover{color:var(--mm-item-ch)}
.adremm-mm__view .sub-menu a:hover{color:var(--mm-sub-ch)}
.adremm-mm__view a:active{color:var(--mm-item-ca)}
.adremm-mm__view .sub-menu a:active{color:var(--mm-sub-ca)}

.adremm-mm__view a:hover:after, .adremm-mm__view a.is-active:after{
  content:'';position:absolute;left:var(--mm-item-px);right:var(--mm-item-px);
  bottom:var(--mm-item-ulo);border-bottom:var(--mm-item-ult) var(--mm-item-ul) currentColor;
}
.adremm-mm__view .sub-menu a:hover:after, .adremm-mm__view .sub-menu a.is-active:after{
  content:'';position:absolute;left:var(--mm-sub-px);right:var(--mm-sub-px);
  bottom:var(--mm-sub-ulo);border-bottom:var(--mm-sub-ult) var(--mm-sub-ul) currentColor;
}

.adremm-mm__next{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid var(--mm-panel-border);background:transparent;margin-left:10px;cursor:pointer}
.adremm-mm__ind{width:var(--mm-ind-size,14px);height:var(--mm-ind-size,14px);background-repeat:no-repeat;background-position:center;background-size:contain;opacity:.9}

.adremm-mm__back{border:1px solid var(--mm-panel-border);background:transparent;color:var(--mm-back-c);padding:var(--mm-label-py,10px) var(--mm-label-px,12px);cursor:pointer;display:inline-flex;gap:10px;align-items:center;font-family:var(--mm-back-ff);font-size:var(--mm-back-fs);font-weight:var(--mm-back-fw);line-height:var(--mm-back-lh);position:relative}
.adremm-mm__back:hover{color:var(--mm-back-ch)}
.adremm-mm__back:active{color:var(--mm-back-ca)}
.adremm-mm__back:hover:after,.adremm-mm__back.is-active:after{content:'';position:absolute;left:12px;right:12px;bottom:var(--mm-back-ulo);border-bottom:var(--mm-back-ult) var(--mm-back-ul) currentColor}
.adremm-mm__backicon{width:var(--mm-back-ind-size,14px);height:var(--mm-back-ind-size,14px);background-repeat:no-repeat;background-position:center;background-size:contain;opacity:.9}
/* Back button visibility (respects back_position) */
.adremm-mm__back--header,.adremm-mm__back--footer{display:none}
.adremm-mm--in-sub.adremm-mm--back-header .adremm-mm__back--header{display:inline-flex}
.adremm-mm--in-sub.adremm-mm--back-footer .adremm-mm__back--footer{display:inline-flex}
.adremm-mm--in-sub.adremm-mm--back-replace_close .adremm-mm__back--header{display:inline-flex}


.adremm-mm__bottom{border-top:1px solid var(--mm-panel-border);padding:10px;display:flex;flex-direction:column;gap:10px}
.adremm-mm__socials{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-start}
.adremm-mm__socials a{width:var(--mm-social-size);height:var(--mm-social-size);display:inline-flex;align-items:center;justify-content:center;color:var(--mm-item-c)}
.adremm-mm__socials svg{width:100%;height:100%}
.adremm-mm__credits a{display:inline-flex;align-items:center;gap:10px;color:var(--mm-item-c)}
.adremm-mm__credits img,.adremm-mm__credits svg{width:var(--mm-credits-size);height:var(--mm-credits-size)}
.adremm-mm.is-open .adremm-mm__overlay{opacity:1}
.adremm-mm.is-open .adremm-mm__panel{transform:translateX(0)}
.adremm-mm--drawer-left.is-open .adremm-mm__panel{transform:translateX(0)}
.adremm-mm--drawer-fullscreen.is-open .adremm-mm__panel{transform:translateY(0)}
.adremm-mm--back-replace_close .adremm-mm__close{display:none}
.adremm-mm[data-showclose='0'] .adremm-mm__close{display:none}

.adremm-mm.is-open .adremm-mm__burger i{background:var(--mm-burger-on)}
.adremm-mm.is-open[style*='--mm-anim:swap'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg)}
.adremm-mm.is-open[style*='--mm-anim:swap'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg)}
.adremm-mm.is-open[style*='--mm-anim:squeeze'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg) scaleX(1.1)}
.adremm-mm.is-open[style*='--mm-anim:squeeze'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg) scaleX(1.1)}
.adremm-mm.is-open[style*='--mm-anim:morph'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg) scaleX(.85)}
.adremm-mm.is-open[style*='--mm-anim:morph'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg) scaleX(.85)}
.adremm-mm[style*='--mm-anim:spin'] .adremm-mm__burger{transition:transform .35s var(--mm-ease)}
.adremm-mm.is-open[style*='--mm-anim:spin'] .adremm-mm__burger{transform:rotate(180deg)}
.adremm-mm.is-open[style*='--mm-anim:spin'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg)}
.adremm-mm.is-open[style*='--mm-anim:spin'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg)}
.adremm-mm.is-open[style*='--mm-anim:arrow'] .adremm-mm__burger i:first-child{top:7px;transform:rotate(45deg) translateX(1px)}
.adremm-mm.is-open[style*='--mm-anim:arrow'] .adremm-mm__burger i:last-child{bottom:7px;transform:rotate(-45deg) translateX(1px)}
";
  wp_register_style('adremm-mmp-front', false, array(), ADREMM_MMP_VERSION);
  wp_enqueue_style('adremm-mmp-front');
  wp_add_inline_style('adremm-mmp-front', $css);

  $platforms = adremm_mmp_social_platforms();
  $social_payload = array();
  foreach($platforms as $k=>$p){
    $row = $o['socials'][$k] ?? array();
    if((($row['on'] ?? '0') === '1')){
      $social_payload[] = array(
        'label'=>$p['label'],
        'url'=>($row['url'] ?? '') ? $row['url'] : '#',
        'svg'=>$p['svg'],
        'custom'=>$row['custom_icon_url'] ?? ''
      );
    }
  }
  $credits_payload = array(
    'on'=>$o['credits_on'],
    'text'=>$o['credits_text'],
    'url'=>$o['credits_url'],
    'icon'=>$o['credits_icon_url'],
  );

  $js = <<<'ADREMM_FRONT_JS'
(function(){
  var socials = ".wp_json_encode($social_payload).";
  var credits = ".wp_json_encode($credits_payload).";

  function svgData(name){
    var svg='';
	    if(name==='chevron') svg='<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 12 12\"><path d=\"M4 2l4 4-4 4\" stroke=\"currentColor\" stroke-width=\"2\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>';
	    if(name==='caret') svg='<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 12 12\"><path fill=\"currentColor\" d=\"M4 2l4 4-4 4\"/></svg>';
	    if(name==='plus') svg='<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 12 12\"><path fill=\"currentColor\" d=\"M5 2h2v8H5zM2 5h8v2H2z\"/></svg>';
	    return 'url(data:image/svg+xml;utf8,' + encodeURIComponent(svg) + ')';
  }

  function getVar(el, name){
    return (getComputedStyle(el).getPropertyValue(name)||'').trim();
  }

  function setIndicatorBg(ind, type, urlVar){
    if(type==='custom' && urlVar && urlVar!=='none'){
      ind.style.backgroundImage = urlVar;
    } else {
      ind.style.backgroundImage = svgData(type);
    }
  }

  function buildSocials(root){
    var wrap = root.querySelector('.adremm-mm__socials');
    if(!wrap) return;
    if(root.getAttribute('data-socials')!=='1' || !socials.length){ wrap.hidden=true; return; }
    wrap.innerHTML='';
    socials.forEach(function(s){
      var a = document.createElement('a');
      a.href = s.url;
      a.target = '_blank';
      a.rel = 'noopener noreferrer';
      a.setAttribute('aria-label', s.label);
      if(a.getAttribute('href')==='#'){ a.addEventListener('click', function(ev){ ev.preventDefault(); }); }
      if(s.custom){
        var img = document.createElement('img');
        img.src = s.custom;
        img.alt = s.label;
        img.style.width='100%';
        img.style.height='100%';
        a.appendChild(img);
      } else {
        a.innerHTML = s.svg;
      }
      wrap.appendChild(a);
    });
    wrap.hidden=false;
  }

  function buildCredits(root){
    var wrap = root.querySelector('.adremm-mm__credits');
    if(!wrap) return;
    if(root.getAttribute('data-credits')!=='1' || !credits || credits.on!=='1'){ wrap.hidden=true; return; }
    wrap.innerHTML='';
    var a = document.createElement('a');
    a.href = credits.url || '#';
    a.target = '_blank';
    a.rel = 'noopener noreferrer';
    if(credits.icon){
      var img = document.createElement('img');
      img.src = credits.icon;
      img.alt = credits.text || 'Credits';
      a.appendChild(img);
    }
    var span = document.createElement('span');
    span.textContent = credits.text || '';
    a.appendChild(span);
    wrap.appendChild(a);
    wrap.hidden=false;
  }

  function init(root){
    if(root.dataset.adremmInit) return;
    root.dataset.adremmInit='1';

    var toggle = root.querySelector('.adremm-mm__toggle');
    var panel  = root.querySelector('.adremm-mm__panel');
    var overlay= root.querySelector('.adremm-mm__overlay');
    var closeBtn = root.querySelector('.adremm-mm__close');
    var views  = root.querySelector('.adremm-mm__views');
    var backHeader = root.querySelector('.adremm-mm__back--header');
    var backFooter = root.querySelector('.adremm-mm__back--footer');

    var stack = [0];

    function openPanel(){
      root.classList.add('is-open');
      panel.hidden=false; overlay.hidden=false;
    }
    function closePanel(){
      root.classList.remove('is-open');
      panel.hidden=true; overlay.hidden=true;
      stack=[0];
      if(views) views.style.transform='translateX(0)';
      if(backHeader) backHeader.hidden=true;
      if(backFooter) backFooter.hidden=true;
      root.classList.remove('adremm-mm--in-sub');
    }

    if(toggle) toggle.addEventListener('click', function(e){ e.preventDefault(); root.classList.contains('is-open')?closePanel():openPanel(); });
    if(overlay) overlay.addEventListener('click', function(e){ e.preventDefault(); closePanel(); });
    if(closeBtn) closeBtn.addEventListener('click', function(e){ e.preventDefault(); closePanel(); });

    var rootView = root.querySelector('.adremm-mm__view.is-root');
    if(!rootView || !views) return;

    var subIconType = root.getAttribute('data-subicon') || 'chevron';
    var backIconType= root.getAttribute('data-backicon') || 'chevron';

    var bIconUrl = getVar(root, '--mm-back-icon-url');
    root.querySelectorAll('.adremm-mm__backicon').forEach(function(ind){
      setIndicatorBg(ind, backIconType, bIconUrl);
    });

    function updateBack(){
      var show = (stack.length > 1);
      if(show){ root.classList.add('adremm-mm--in-sub'); } else { root.classList.remove('adremm-mm--in-sub'); }
      if(backHeader && (root.classList.contains('adremm-mm--back-header') || root.classList.contains('adremm-mm--back-replace_close'))) backHeader.hidden = !show;
      if(backFooter && root.classList.contains('adremm-mm--back-footer')) backFooter.hidden = !show;
    }

    function prepareView(viewEl){
      if(!viewEl) return;
      viewEl.querySelectorAll('li').forEach(function(li){
        if(li.querySelector(':scope > ul')) li.classList.add('menu-item-has-children');
      });

      var items = Array.prototype.slice.call(viewEl.querySelectorAll('li.menu-item-has-children'));
      items.forEach(function(li){
        if(li.dataset.adremmPrep) return;
        var submenu = li.querySelector(':scope > ul');
        if(!submenu) return;

        var view = document.createElement('div');
        view.className='adremm-mm__view';
        view.appendChild(submenu);
        views.appendChild(view);
        var viewIndex = views.children.length - 1;
        li.dataset.adremmPrep='1';

        var a = li.querySelector(':scope > a');
        if(a){
          var btn = document.createElement('button');
          btn.type='button';
          btn.className='adremm-mm__next';
          var ind = document.createElement('span');
          ind.className='adremm-mm__ind';
          var iconUrl = getVar(root,'--mm-icon-url');
          setIndicatorBg(ind, subIconType, iconUrl);
          btn.appendChild(ind);
          a.after(btn);

          btn.dataset.target = String(viewIndex);
          btn.addEventListener('click', function(ev){
            ev.preventDefault();
            var t = parseInt(btn.dataset.target||'0',10);
            if(!isFinite(t) || t<0) return;
            stack.push(t);
            views.style.transform='translateX(-'+(t*100)+'%)';
            updateBack();
            prepareView(view);
          });
        }
      });
    }

    prepareView(rootView);
    updateBack();

    function goBack(){
      if(stack.length>1){
        stack.pop();
        var t = stack[stack.length-1] || 0;
        views.style.transform='translateX(-'+(t*100)+'%)';
        updateBack();
      }
    }
    if(backHeader) backHeader.addEventListener('click', goBack);
    if(backFooter) backFooter.addEventListener('click', goBack);

    document.addEventListener('keydown', function(ev){
      if(ev.key==='Escape' && root.classList.contains('is-open')) closePanel();
    });

    buildSocials(root);
    buildCredits(root);
  }


  function boot(){ document.querySelectorAll('.adremm-mm').forEach(init); }
  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
ADREMM_FRONT_JS;

  wp_register_script('adremm-mmp-front', false, array(), ADREMM_MMP_VERSION, true);
  wp_enqueue_script('adremm-mmp-front');
  wp_add_inline_script('adremm-mmp-front', $js);
}