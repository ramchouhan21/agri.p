<?php
// Language system for multilingual support
$current_lang = $_SESSION['language'] ?? 'en';

// Language arrays
$languages = [
    'en' => 'English',
    'hi' => 'हिन्दी',
    'te' => 'తెలుగు',
    'ta' => 'தமிழ்',
    'bn' => 'বাংলা'
];

// Language strings
$lang = [];

if ($current_lang === 'hi') {
    $lang = [
        'home_title' => 'स्मार्ट कृषि प्रणाली',
        'hero_title' => 'किसानों और खरीदारों को जोड़ने वाला डिजिटल प्लेटफॉर्म',
        'hero_subtitle' => 'पारदर्शी मूल्य निर्धारण, सीधा संपर्क, और सरकारी समर्थन के साथ',
        'register_farmer' => 'किसान रजिस्टर करें',
        'register_buyer' => 'खरीदार रजिस्टर करें',
        'features_title' => 'मुख्य विशेषताएं',
        'feature_crop_management' => 'फसल प्रबंधन',
        'feature_crop_desc' => 'अपनी फसलों को डिजिटल रूप से प्रबंधित करें',
        'feature_price_tracking' => 'मूल्य ट्रैकिंग',
        'feature_price_desc' => 'वास्तविक समय में बाजार मूल्य की निगरानी करें',
        'feature_logistics' => 'लॉजिस्टिक्स',
        'feature_logistics_desc' => 'कुशल परिवहन और वितरण',
        'feature_government' => 'सरकारी समर्थन',
        'feature_gov_desc' => 'MSP और सरकारी नीतियों का लाभ उठाएं',
        'registered_farmers' => 'पंजीकृत किसान',
        'active_buyers' => 'सक्रिय खरीदार',
        'crop_varieties' => 'फसल किस्में',
        'total_transactions' => 'कुल लेनदेन',
        'about_title' => 'हमारे बारे में',
        'about_subtitle' => 'स्मार्ट कृषि प्रणाली के बारे में जानें',
        'our_mission' => 'हमारा मिशन',
        'mission_text' => 'किसानों और खरीदारों के बीच एक पारदर्शी, निष्पक्ष और कुशल मंच प्रदान करना।',
        'our_vision' => 'हमारी दृष्टि',
        'vision_text' => 'भारतीय कृषि को डिजिटल रूप से सशक्त बनाना और सभी हितधारकों के लिए मूल्य सृजन करना।',
        'key_benefits' => 'मुख्य लाभ',
        'benefit_transparency' => 'पूर्ण पारदर्शिता',
        'benefit_fair_pricing' => 'निष्पक्ष मूल्य निर्धारण',
        'benefit_direct_connection' => 'किसानों से सीधा संपर्क',
        'benefit_government_support' => 'सरकारी समर्थन और MSP',
        'benefit_quality_assurance' => 'गुणवत्ता आश्वासन',
        'our_team' => 'हमारी टीम',
        'tech_team' => 'तकनीकी टीम',
        'tech_team_desc' => 'अनुभवी डेवलपर्स और डिजाइनर',
        'agriculture_experts' => 'कृषि विशेषज्ञ',
        'agriculture_experts_desc' => 'कृषि क्षेत्र के विशेषज्ञ',
        'government_partners' => 'सरकारी भागीदार',
        'government_partners_desc' => 'सरकारी विभागों के साथ सहयोग',
        'contact_title' => 'संपर्क करें',
        'contact_subtitle' => 'हमसे संपर्क करने के लिए नीचे दिया गया फॉर्म भरें',
        'get_in_touch' => 'संपर्क में रहें',
        'address' => 'पता',
        'phone' => 'फोन',
        'email' => 'ईमेल',
        'working_hours' => 'कार्य समय',
        'send_message' => 'संदेश भेजें',
        'name' => 'नाम',
        'subject' => 'विषय',
        'message' => 'संदेश',
        'contact_success' => 'आपका संदेश सफलतापूर्वक भेजा गया!',
        'contact_error' => 'कृपया सभी आवश्यक फ़ील्ड भरें।'
    ];
} elseif ($current_lang === 'te') {
    $lang = [
        'home_title' => 'స్మార్ట్ వ్యవసాయ వ్యవస్థ',
        'hero_title' => 'రైతులు మరియు కొనుగోలుదారులను కలిపే డిజిటల్ ప్లాట్‌ఫారమ్',
        'hero_subtitle' => 'పారదర్శక ధరలు, నేరుగా కనెక్షన్, మరియు ప్రభుత్వ మద్దతుతో',
        'register_farmer' => 'రైతును నమోదు చేయండి',
        'register_buyer' => 'కొనుగోలుదారును నమోదు చేయండి',
        'features_title' => 'ప్రధాన లక్షణాలు',
        'feature_crop_management' => 'పంట నిర్వహణ',
        'feature_crop_desc' => 'మీ పంటలను డిజిటల్‌గా నిర్వహించండి',
        'feature_price_tracking' => 'ధర ట్రాకింగ్',
        'feature_price_desc' => 'రియల్ టైమ్‌లో మార్కెట్ ధరలను పర్యవేక్షించండి',
        'feature_logistics' => 'లాజిస్టిక్స్',
        'feature_logistics_desc' => 'సమర్థవంతమైన రవాణా మరియు డెలివరీ',
        'feature_government' => 'ప్రభుత్వ మద్దతు',
        'feature_gov_desc' => 'MSP మరియు ప్రభుత్వ విధానాల ప్రయోజనం పొందండి',
        'registered_farmers' => 'నమోదైన రైతులు',
        'active_buyers' => 'క్రియాశీల కొనుగోలుదారులు',
        'crop_varieties' => 'పంట రకాలు',
        'total_transactions' => 'మొత్తం లావాదేవీలు',
        'about_title' => 'మా గురించి',
        'about_subtitle' => 'స్మార్ట్ వ్యవసాయ వ్యవస్థ గురించి తెలుసుకోండి',
        'our_mission' => 'మా లక్ష్యం',
        'mission_text' => 'రైతులు మరియు కొనుగోలుదారుల మధ్య పారదర్శక, న్యాయమైన మరియు సమర్థవంతమైన వేదికను అందించడం.',
        'our_vision' => 'మా దృష్టి',
        'vision_text' => 'భారతీయ వ్యవసాయాన్ని డిజిటల్‌గా శక్తివంతం చేయడం మరియు అన్ని వాటాదారులకు విలువ సృష్టించడం.',
        'key_benefits' => 'ప్రధాన ప్రయోజనాలు',
        'benefit_transparency' => 'పూర్తి పారదర్శకత',
        'benefit_fair_pricing' => 'న్యాయమైన ధరలు',
        'benefit_direct_connection' => 'రైతులతో నేరుగా కనెక్షన్',
        'benefit_government_support' => 'ప్రభుత్వ మద్దతు మరియు MSP',
        'benefit_quality_assurance' => 'నాణ్యత హామీ',
        'our_team' => 'మా బృందం',
        'tech_team' => 'సాంకేతిక బృందం',
        'tech_team_desc' => 'అనుభవజ్ఞులైన డెవలపర్లు మరియు డిజైనర్లు',
        'agriculture_experts' => 'వ్యవసాయ నిపుణులు',
        'agriculture_experts_desc' => 'వ్యవసాయ రంగ నిపుణులు',
        'government_partners' => 'ప్రభుత్వ భాగస్వాములు',
        'government_partners_desc' => 'ప్రభుత్వ విభాగాలతో సహకారం',
        'contact_title' => 'సంప్రదించండి',
        'contact_subtitle' => 'మమ్మల్ని సంప్రదించడానికి దిగువ ఫారమ్‌ను పూరించండి',
        'get_in_touch' => 'సంప్రదించండి',
        'address' => 'చిరునామా',
        'phone' => 'ఫోన్',
        'email' => 'ఇమెయిల్',
        'working_hours' => 'పని గంటలు',
        'send_message' => 'సందేశం పంపండి',
        'name' => 'పేరు',
        'subject' => 'విషయం',
        'message' => 'సందేశం',
        'contact_success' => 'మీ సందేశం విజయవంతంగా పంపబడింది!',
        'contact_error' => 'దయచేసి అన్ని అవసరమైన ఫీల్డ్‌లను పూరించండి.'
    ];
} else {
    // Default English
    $lang = [
        'home_title' => 'Smart Agriculture System',
        'hero_title' => 'Digital Platform Connecting Farmers and Buyers',
        'hero_subtitle' => 'Transparent pricing, direct connection, and government support',
        'register_farmer' => 'Register as Farmer',
        'register_buyer' => 'Register as Buyer',
        'features_title' => 'Key Features',
        'feature_crop_management' => 'Crop Management',
        'feature_crop_desc' => 'Manage your crops digitally',
        'feature_price_tracking' => 'Price Tracking',
        'feature_price_desc' => 'Monitor market prices in real-time',
        'feature_logistics' => 'Logistics',
        'feature_logistics_desc' => 'Efficient transportation and delivery',
        'feature_government' => 'Government Support',
        'feature_gov_desc' => 'Benefit from MSP and government policies',
        'registered_farmers' => 'Registered Farmers',
        'active_buyers' => 'Active Buyers',
        'crop_varieties' => 'Crop Varieties',
        'total_transactions' => 'Total Transactions',
        'about_title' => 'About Us',
        'about_subtitle' => 'Learn about the Smart Agriculture System',
        'our_mission' => 'Our Mission',
        'mission_text' => 'To provide a transparent, fair, and efficient platform connecting farmers and buyers.',
        'our_vision' => 'Our Vision',
        'vision_text' => 'To digitally empower Indian agriculture and create value for all stakeholders.',
        'key_benefits' => 'Key Benefits',
        'benefit_transparency' => 'Complete Transparency',
        'benefit_fair_pricing' => 'Fair Pricing',
        'benefit_direct_connection' => 'Direct Connection with Farmers',
        'benefit_government_support' => 'Government Support & MSP',
        'benefit_quality_assurance' => 'Quality Assurance',
        'our_team' => 'Our Team',
        'tech_team' => 'Technical Team',
        'tech_team_desc' => 'Experienced developers and designers',
        'agriculture_experts' => 'Agriculture Experts',
        'agriculture_experts_desc' => 'Experts in agriculture sector',
        'government_partners' => 'Government Partners',
        'government_partners_desc' => 'Collaboration with government departments',
        'contact_title' => 'Contact Us',
        'contact_subtitle' => 'Fill out the form below to get in touch with us',
        'get_in_touch' => 'Get in Touch',
        'address' => 'Address',
        'phone' => 'Phone',
        'email' => 'Email',
        'working_hours' => 'Working Hours',
        'send_message' => 'Send Message',
        'name' => 'Name',
        'subject' => 'Subject',
        'message' => 'Message',
        'contact_success' => 'Your message has been sent successfully!',
        'contact_error' => 'Please fill in all required fields.'
    ];
}

// Language switching function
function switchLanguage($lang_code) {
    $_SESSION['language'] = $lang_code;
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Handle language switching
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $languages)) {
    switchLanguage($_GET['lang']);
}
?>
