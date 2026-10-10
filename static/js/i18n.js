/**
 * Nepal Online Voting System - Bilingual (EN / नेपाली) Engine
 * Seamlessly translates UI elements between English and Nepali
 * and persists voter preference in localStorage.
 */

(function () {
  'use strict';

  // Comprehensive phrase translations
  const TRANSLATIONS = {
    // Navigation & Global UI
    "Home": "गृहपृष्ठ",
    "Elections": "निर्वाचनहरू",
    "Candidates": "उम्मेदवारहरू",
    "Results": "नतिजाहरू",
    "About": "हाम्रोबारे",
    "Admin Panel": "प्रशासक प्यानल",
    "Dashboard": "ड्यासबोर्ड",
    "Logout": "लगआउट",
    "Voter Login": "मतदाता लगइन",
    "New Voter Registration": "नयाँ मतदाता दर्ता",
    "Public Site": "सार्वजनिक पोर्टल",
    "My Elections": "मेरो निर्वाचन",
    "Voting Status": "मतदान स्थिति",
    "Profile": "प्रोफाइल",
    "Search elections, candidates...": "निर्वाचन, उम्मेदवार खोज्नुहोस्...",
    "Search elections...": "निर्वाचन खोज्नुहोस्...",
    "Search by name, party, district, or constituency...": "नाम, दल, जिल्ला वा क्षेत्र खोज्नुहोस्...",
    "Filter by Party": "दल अनुसार फिल्टर गर्नुहोस्",
    "All Parties": "सबै राजनीतिक दलहरू",
    "All Elections": "सबै निर्वाचन",
    "Active (Voting Open)": "सक्रिय (मतदान जारी)",
    "Upcoming": "आगामी",
    "Completed": "सम्पन्न",
    "All": "सबै",
    "Active": "सक्रिय",
    "Starts Soon": "चाँडै सुरु हुँदैछ",
    "Voting Open": "मतदान जारी",
    "Voted": "मतदान सम्पन्न",
    "Vote Now": "अहिले मतदान गर्नुहोस्",
    "View Details": "विवरण हेर्नुहोस्",
    "View Results": "नतिजा हेर्नुहोस्",
    "Explore Candidates →": "उम्मेदवारहरू हेर्नुहोस् →",
    "Explore Candidates": "उम्मेदवारहरू हेर्नुहोस् →",
    "Explore Candidates": "उम्मेदवारहरू हेर्नुहोस्",
    "Audit Status Log →": "अडिट स्थिति लग →",
    "Audit Status Log": "अडिट स्थिति लग →",
    "My Dashboard": "मेरो ड्यासबोर्ड",

    // Brand & Motto
    "Nepal Online Voting System": "नेपाल अनलाइन मतदान प्रणाली",
    "Your Vote, Our Future": "तपाईंको मत, हाम्रो भविष्य",
    "Together for a Stronger Democracy in Nepal": "नेपालमा बलियो लोकतन्त्रको लागि सँगै",
    "Verified Voter": "प्रमाणित मतदाता",
    "Voter Portal": "मतदाता पोर्टल",
    "National Democratic Online Voting System": "राष्ट्रिय लोकतान्त्रिक अनलाइन मतदान प्रणाली",
    "Federal Democratic Republic": "संघीय लोकतान्त्रिक गणतन्त्र",
    "Nepal • नेपाल": "नेपाल सरकार • निर्वाचन आयोग",
    "100% Encrypted Ballot Secrecy": "१००% ईन्क्रिप्टेड मत गोपनीयता",
    "Election Commission Standard": "निर्वाचन आयोगको मापदण्ड",
    "Live 2083/2026": "प्रत्यक्ष २०८३/२०२६",
    "256-Bit Ballot Secrecy": "२५६-बिट मत गोपनीयता",
    "18+ Verified Voters (Art. 84)": "१८+ प्रमाणित मतदाता (धारा ८४)",
    "One Person • One Vote": "एक नागरिक • एक मत",
    "1 Citizen = 1 Vote": "१ नागरिक = १ मत",
    "1 Vote Cast": "१ मत खसाइसकियो",

    // Homepage Hero Section
    "Secure, Transparent & Accessible Digital Elections for Nepal": "नेपालको लागि सुरक्षित, पारदर्शी र भरपर्दो डिजिटल निर्वाचन प्रणाली",
    "Official digital balloting portal for Nepali citizens. Exercise your democratic constitutional right with guaranteed 256-bit encryption, strict biometric/citizenship verification, and real-time auditable integrity.": "नेपाली नागरिकहरूको लागि आधिकारिक डिजिटल मतदान पोर्टल। संवैधानिक प्रावधान अनुसार गोप्य, पारदर्शी र पूर्ण सुरक्षित अनलाइन मतदान।",

    // Homepage Key Features
    "Key Features": "प्रणालीका मुख्य विशेषताहरू",
    "Why Choose Our System?": "हाम्रो मतदान प्रणाली किन रोज्ने?",
    "Engineered specifically to solve the logistical and accessibility challenges of general elections across all seven provinces of Nepal.": "नेपालका सातै प्रदेशमा आम निर्वाचनका भौगोलिक र प्राविधिक चुनौतीहरू समाधान गर्न विशेष रूपमा निर्माण गरिएको प्रणाली।",
    "Constitutional Ballot Secrecy": "संवैधानिक मत गोपनीयता",
    "Military-grade SHA-256 and AES encryption guarantees that your vote cannot be linked to your identity by anyone, including system administrators.": "आधुनिक क्रिप्टोग्राफिक सुरक्षा प्रणाली जसले मतदाताको व्यक्तिगत पहिचान र गोप्य मतलाई पूर्ण रूपमा अलग राख्छ।",
    "Citizenship Verification": "नागरिकता प्रमाणीकरण",
    "Direct integration with the national citizenship database to ensure only verified, registered Nepali citizens above 18 years can vote.": "राष्ट्रिय नागरिकता अभिलेखसँग प्रत्यक्ष प्रमाणीकरण गरी १८ वर्ष पुगेका योग्य नागरिकहरूलाई मात्र मतदान अधिकार प्रदान गरिन्छ।",
    "Tamper-Evident Ledger": "सुरक्षित डिजिटल अडिट",
    "Votes are cryptographically recorded in an immutable audit ledger, preventing vote tampering, double-voting, or ballot destruction.": "हरेक मत अपरिवर्तनीय डिजिटल लेजरमा सुरक्षित गरिन्छ, जसले दोहोरो मतदान र धाँधली पूर्ण रूपमा रोक्छ।",
    "Total Accessibility": "पूर्ण पहुँचयोग्यता",
    "Vote securely from your home, workplace, or anywhere across Nepal or abroad, dramatically increasing democratic voter turnout.": "घर, कार्यालय वा विदेश जहाँसुकैबाट सजिलै मतदान गर्न सकिने, जसले सहभागितालाई उल्लेख्य वृद्धि गर्छ।",
    "Audit & Transparency": "पारदर्शी मतगणना",
    "Real-time cryptographic audit trail allows electoral observers and political parties to independently verify vote integrity.": "स्वतन्त्र निर्वाचन पर्यवेक्षक र दलहरूले मत परिणामको शुद्धता तत्काल परीक्षण गर्न सक्ने पारदर्शी व्यवस्था।",
    "Voter Participation Receipt": "मतदान प्रमाणीकरण रसिद",
    "Every voter receives an encrypted unique receipt code to independently verify their vote was recorded without exposing their choice.": "मतदान सम्पन्न भएपछि प्रत्येक मतदातालाई गोप्य डिजिटल रसिद कोड प्राप्त हुन्छ।",

    // How The System Works
    "How The System Works": "प्रणालीले कसरी काम गर्छ?",
    "A simple, constitutional, three-step digital voting journey": "संवैधानिक र सरल ३-चरणको डिजिटल मतदान प्रक्रिया",
    "Step 1: Voter Identity Verification": "चरण १: नागरिकता तथा मतदाता पहिचान",
    "Login with your unique Voter ID and Citizenship Number. The system cross-references national voter records to confirm your legal eligibility.": "तपाईंको मतदाता परिचयपत्र र नागरिकता नम्बरबाट लगइन गर्नुहोस्। प्रणालीले योग्यता स्वचालित रूपमा प्रमाणित गर्दछ।",
    "Step 2: Cast Your Encrypted Vote": "चरण २: सुरक्षित डिजिटल मतदान",
    "Select your candidate of choice. Your selection is immediately encrypted before leaving your browser, ensuring absolute ballot secrecy.": "आफ्नो रोजाइको उम्मेदवार छनोट गर्नुहोस्। तपाईंको मत तुरुन्तै ईन्क्रिप्ट भई पूर्ण गोप्य रहन्छ।",
    "Step 3: Cryptographic Audit Receipt": "चरण ३: डिजिटल मतदान प्रमाण",
    "Receive an instant cryptographic receipt confirming your vote is tallied. Your personal choice is permanently concealed under national election law.": "तपाईंको मत गणनामा समावेश भएको तत्काल डिजिटल रसिद प्राप्त गर्नुहोस्। रोजाइ पूर्ण गोप्य रहन्छ।",
    "Ready to Participate in Democratic Digital Voting?": "नेपालको डिजिटल लोकतन्त्रमा सहभागी हुन तयार हुनुहुन्छ?",
    "Join thousands of registered citizens already enrolled in the digital voter register.": "डिजिटल मतदाता नामावलीमा दर्ता भएका हजारौं सचेत नेपाली नागरिकहरूसँग जोडिनुहोस्।",
    "Register as Voter": "मतदाता दर्ता गर्नुहोस्",
    "Official Election Commission Architecture": "निर्वाचन आयोगको आधिकारिक प्रणाली",

    // Elections & Candidate Pages
    "Elections in Nepal": "नेपालका निर्वाचनहरू",
    "Official federal, provincial and local elections conducted under Election Commission standards": "निर्वाचन आयोगको मापदण्ड अनुसार सञ्चालन हुने आधिकारिक संघीय, प्रादेशिक र स्थानीय निर्वाचनहरू",
    "Elections Directory": "निर्वाचन नामावली",
    "Official federal, provincial, and local election contests": "आधिकारिक संघीय, प्रादेशिक तथा स्थानीय निर्वाचनहरू",
    "One Citizenship, One Vote Rule: Official Ballot Recorded": "एक नागरिकता, एक मत नियम: आधिकारिक मत दर्ता भएको छ",
    "Contesting Candidates": "उम्मेदवारहरू",
    "Nominated Candidates": "मनोनित उम्मेदवारहरू",
    "Audited": "लेखापरीक्षण सम्पन्न",
    "Results Certified": "प्रमाणित अन्तिम नतिजा",
    "Completed & Audited Elections": "सम्पन्न तथा प्रमाणित निर्वाचनहरू",
    "Official final results certified by the Election Commission audit protocol": "निर्वाचन आयोगको अडिट प्रोटोकलद्वारा प्रमाणित आधिकारिक अन्तिम नतिजाहरू",
    "Official Election Results": "आधिकारिक निर्वाचन परिणाम",
    "Audited election tabulations and democratic outcomes": "लेखापरीक्षण गरिएका निर्वाचन परिणामहरू",

    // Footer
    "Quick Links": "द्रुत लिङ्कहरू",
    "Security Architecture": "सुरक्षा संरचना",
    "Check Voting Status": "मतदान अवस्था जाँच",
    "Administrative Portal": "प्रशासकीय पोर्टल",
    "Security & Trust": "सुरक्षा तथा विश्वसनीयता",
    "Ballot Secrecy Protocol": "मत गोपनीयता प्रोटोकल",
    "End-to-End Audit Logs": "पूर्ण अडिट प्रणाली",
    "Voter ID Verification": "मतदाता प्रमाणपत्र प्रमाणीकरण",
    "Election Commission Standards": "निर्वाचन आयोगको मापदण्ड",
    "Academic BCA Project Demo • Non-partisan Simulation": "शैक्षिक BCA परियोजना प्रदर्शन • निष्पक्ष लोकतान्त्रिक अभ्यास",
    "Academic BCA Project Demo": "शैक्षिक BCA परियोजना प्रदर्शन",
    "Non-partisan Simulation": "निष्पक्ष लोकतान्त्रिक अभ्यास",
    "Designed for academic demonstration and viva presentation.": "शैक्षिक प्रदर्शन तथा मौखिक परीक्षा (Viva) को लागि तयार गरिएको।"
  };

  // Reverse mapping for restoring English
  const REVERSE_TRANSLATIONS = {};
  for (const [en, ne] of Object.entries(TRANSLATIONS)) {
    REVERSE_TRANSLATIONS[ne] = en;
  }

  const STORAGE_KEY = 'voting_system_lang_preference';

  /**
   * Translates a single DOM text node or element attribute
   */
  function translateText(text, targetLang) {
    if (!text) return text;
    const trimmed = text.trim();
    if (!trimmed) return text;

    if (targetLang === 'ne') {
      if (TRANSLATIONS[trimmed]) {
        return text.replace(trimmed, TRANSLATIONS[trimmed]);
      }
    } else {
      if (REVERSE_TRANSLATIONS[trimmed]) {
        return text.replace(trimmed, REVERSE_TRANSLATIONS[trimmed]);
      }
    }
    return text;
  }

  /**
   * Recursively traverses DOM and translates text
   */
  function translateDOM(targetLang) {
    document.documentElement.lang = targetLang;

    // 1. Walk through all text nodes inside body (ignoring script & style)
    const walker = document.createTreeWalker(
      document.body,
      NodeFilter.SHOW_TEXT,
      {
        acceptNode: function (node) {
          const parentTag = node.parentElement ? node.parentElement.tagName.toLowerCase() : '';
          if (parentTag === 'script' || parentTag === 'style' || parentTag === 'noscript') {
            return NodeFilter.FILTER_REJECT;
          }
          if (node.parentElement && node.parentElement.closest('.lang-switch')) {
            return NodeFilter.FILTER_REJECT;
          }
          return NodeFilter.FILTER_ACCEPT;
        }
      },
      false
    );

    let node;
    while ((node = walker.nextNode())) {
      const original = node.nodeValue;
      const translated = translateText(original, targetLang);
      if (translated !== original) {
        node.nodeValue = translated;
      }
    }

    // 2. Translate Input Placeholders
    const inputs = document.querySelectorAll('input[placeholder], textarea[placeholder]');
    inputs.forEach(input => {
      const currentPlaceholder = input.getAttribute('placeholder');
      if (currentPlaceholder) {
        const translated = translateText(currentPlaceholder, targetLang);
        if (translated !== currentPlaceholder) {
          input.setAttribute('placeholder', translated);
        }
      }
    });

    // 3. Update active pill indicators in all .lang-switch controls
    document.querySelectorAll('.lang-switch').forEach(sw => {
      const enOpt = sw.querySelector('[data-lang="en"]');
      const neOpt = sw.querySelector('[data-lang="ne"]');
      if (enOpt && neOpt) {
        if (targetLang === 'ne') {
          enOpt.classList.remove('active');
          neOpt.classList.add('active');
        } else {
          neOpt.classList.remove('active');
          enOpt.classList.add('active');
        }
      }
    });
  }

  /**
   * Shows a smooth floating toast notification
   */
  function showToast(message, isNepali) {
    let toast = document.getElementById('langToastNotification');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'langToastNotification';
      toast.className = 'lang-toast';
      document.body.appendChild(toast);
    }
    toast.innerHTML = `
      <span>${message}</span>
    `;
    toast.classList.add('show');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
      toast.classList.remove('show');
    }, 2800);
  }

  /**
   * Sets current language and triggers translation + feedback
   */
  function setLanguage(lang, showNotification = true) {
    const currentLang = localStorage.getItem(STORAGE_KEY) || 'en';
    if (lang === currentLang && !showNotification) {
      // Even if unchanged, ensure buttons reflect it
      translateDOM(lang);
      return;
    }

    localStorage.setItem(STORAGE_KEY, lang);
    translateDOM(lang);

    if (showNotification) {
      if (lang === 'ne') {
        showToast('नेपाली भाषा सक्रिय गरियो (Switched to Nepali)', true);
      } else {
        showToast('Switched to English interface', false);
      }
    }
  }

  /**
   * Initialize language switcher click handlers
   */
  function initLanguageSwitcher() {
    const savedLang = localStorage.getItem(STORAGE_KEY) || 'en';

    // Apply saved language on initial load
    if (savedLang === 'ne') {
      setLanguage('ne', false);
    } else {
      translateDOM('en');
    }

    // Attach listeners to all language switcher components
    document.querySelectorAll('.lang-switch').forEach(switcher => {
      switcher.style.cursor = 'pointer';

      // Click on individual language option pills
      const enBtn = switcher.querySelector('[data-lang="en"]');
      const neBtn = switcher.querySelector('[data-lang="ne"]');

      if (enBtn) {
        enBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          setLanguage('en', true);
        });
      }

      if (neBtn) {
        neBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          setLanguage('ne', true);
        });
      }

      // Clicking anywhere on the switcher toggles between en and ne
      switcher.addEventListener('click', function () {
        const cur = localStorage.getItem(STORAGE_KEY) || 'en';
        const next = cur === 'en' ? 'ne' : 'en';
        setLanguage(next, true);
      });
    });
  }

  // Run on DOM Ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLanguageSwitcher);
  } else {
    initLanguageSwitcher();
  }

  // Expose global helper
  window.NepalVotingI18n = {
    setLanguage: setLanguage,
    getCurrentLanguage: () => localStorage.getItem(STORAGE_KEY) || 'en'
  };
})();
