# php-tracking-pixel
Simple tracking pixel written in php.

# Features
- write log file.
- send replace picture.
- redirect to other url.
- make debugging.
- cleaning old logs.
- send custome post request.
- set custome option.
- add additional text/informations.

# Installation
- Copy the code in your ftp folder. (Ready to use. :) )
- <code> git clone https://github.com/dmd2222/php-tracking-pixel.git <code>
- Optional: change options in the beginning of the main file.
  
  
# Using
  - Only using the tracking pixel:
  - <img src="https://your-domain-folder/tracking_pixel.php" >
  - Make a redirecting to annoter website:
  - https://your-domain-folder/tracking_pixel.php?re=https://earcandle.de/
  - Replace image:
  - https://your-domain-folder/tracking_pixel.php?ri=https://cs-digital-ug.de/images/logo.png
  - <img src="https://your-domain-folder/tracking_pixel.php?ri=https://cs-digital-ug.de/images/logo.png" >
  - Write the additional informations:
  - https://your-domain-folder/tracking_pixel.php?ai=Heyho
  - <img src="https://your-domain-folder/tracking_pixel.php?ai=Heyho" >
  - Send email by pixel call: (EMAIL is coded im base64, like test@test.com -> dGVzdEB0ZXN0LmNvbQ== -- En and Decoder:https://www.base64decode.org/)
  - https://your-domain-folder/tracking_pixel/php-tracking-pixel/tracking_pixel.php?em=dGVzdEB0ZXN0LmNvbQ
  
  
  
# Thank me
 - Work with me on the project.
 - Make sugesstions to improve the script.
 - donate some coffee bucks: 
 -   <a href="https://unze4u.de/UShort/s.php?i=fu" target="_blank"><img src="images/patreon_logo.png" alt="https://unze4u.de/UShort/s.php?i=fu" style="width:100px;height:100px;"></a>
  - <a href="https://unze4u.de/UShort/s.php?i=fu" target="_blank">PATREONS.COM </a>
  - <a href="https://unze4u.de/UShort/s.php?i=fv" target="_blank">PAYPAL </a>
  - LTC(Litcoin):  MLZ3ZDsWd2v5KPq8dVMZWbbsuH3xxZbgh5
  - You dont have Litecoin:
  <div id="iframe-widget-wrapper" style="height: 205px">
      <iframe id='iframe-widget' src='https://changenow.io/embeds/exchange-widget/v2/widget.html?FAQ=true&amount=0.1&from=btc&horizontal=true&lang=en-US&link_id=c77b807c7e1b74&locales=true&logo=true&to=ltc' style="height: 455px; width: 100%; border: none"></iframe>
      <script defer type='text/javascript' src='https://changenow.io/embeds/exchange-widget/v2/stepper-connector.js'></script>
    </div>

  
 # Licence
 Copyright (c) CS-Digital UG (hatungsbeschränkt) https://cs-digital-ug.de/ 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
THE USE OR OTHER DEALINGS IN THE SOFTWARE
  
  
  
  
  #####################################################################
# Haftung (german)
  - §1 Es wird keine haftung für das Projekt übernommen.
  - §2 Sollten einzelne Bestimmungen dieses Vertrages unwirksam oder undurchführbar sein oder nach Vertragsschluss unwirksam oder undurchführbar werden, bleibt davon die Wirksamkeit des Vertrages im Übrigen unberührt. An die Stelle der unwirksamen oder undurchführbaren Bestimmung soll diejenige wirksame und durchführbare Regelung treten, deren Wirkungen der wirtschaftlichen Zielsetzung am nächsten kommen, die die Vertragsparteien mit der unwirksamen bzw. undurchführbaren Bestimmung verfolgt haben. Die vorstehenden Bestimmungen gelten entsprechend für den Fall, dass sich der Vertrag als lückenhaft erweist.
