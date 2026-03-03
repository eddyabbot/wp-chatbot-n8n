=== WP ChatBot N8n ===
Contributors: eddyabbot
Tags: chatbot, n8n, chat widget, automation, ai
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Integra il chatbot di n8n su tutto il sito WordPress con una semplice configurazione dall'admin.

== Description ==

Plugin leggero e zero dipendenze che inietta il widget chatbot di n8n (@n8n/chat) su ogni pagina del sito.

**Come funziona:**

1. Installa e attiva il plugin
2. Vai su Impostazioni → ChatBot N8n
3. Inserisci l'URL del webhook del tuo workflow n8n (con nodo "Chat Trigger")
4. Personalizza titolo, colore, messaggio di benvenuto e posizione
5. Abilita il chatbot e salva

Il widget apparirà su tutto il sito nella posizione configurata.

**Requisiti lato n8n:**
- Workflow n8n attivo con nodo "Chat Trigger"
- URL di produzione del webhook

== Installation ==

1. Carica la cartella `wp-chatbot-n8n` nella directory `/wp-content/plugins/`
2. Attiva il plugin dal menu Plugin di WordPress
3. Vai su Impostazioni → ChatBot N8n e configura il plugin

== Changelog ==

= 1.0.0 =
* Prima versione stabile
* Impostazioni: URL webhook, titolo, colore primario, posizione, messaggio di benvenuto, toggle on/off
