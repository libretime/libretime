---
title: Préparation des médias pour le téléchargement
---

Avant de télécharger des médias sur un serveur LibreTime, il y a un certain nombre de facteurs à prendre en compte. Un bon flux d'ingestion vous fera gagner beaucoup de temps par la suite.

## Qualité des métadonnées

LibreTime importe automatiquement toutes les métadonnées qui se trouvent dans les balises ID3 des fichiers. Si ces balises sont incorrectes ou s'il manque des informations, vous devrez soit éditer les métadonnées manuellement. Les fichiers contenant des métadonnées telles que le titre de la piste et les informations sur l'artiste peuvent être difficiles à localiser dans les grandes bibliothèques, ou à ajouter aux émissions, aux listes de lecture ou aux blocs intelligents.

Il existe un certain nombre de programmes qui peuvent être utilisés pour corriger les erreurs ou les informations incomplètes dans les balises ID3. Vous pouvez également utiliser un gestionnaire de bibliothèque musicale (comme Apple Music, Rhythmbox ou Windows Media Player) pour modifier les balises ID3, mais vous devrez peut-être importer les fichiers dans votre bibliothèque, ce qui n'est pas toujours pratique.

- [TagScanner](https://www.xdlab.ru/en/) (Windows)
- [Mp3tag](https://www.mp3tag.de/en/index.html) (Windows)
- [MusicBrainz Picard](https://picard.musicbrainz.org/) (Mac, Windows, Linux)
- [Ex Falso](https://code.google.com/p/quodlibet/) (Linux)

La fonction "Tags From Path" d'Ex Falso est un gain de temps particulièrement utile si vous avez une grande archive de fichiers non étiquetés. Parfois, il y a des informations utiles sur le créateur ou le titre dans le nom du fichier ou la structure du chemin d'accès au répertoire, qui peuvent être converties automatiquement en balise ID3.

![](./preparing-media-screenshot175-ex_falso.png)

### Métadonnées dans les anciens jeux de caractères

LibreTime s'attend à ce que les métadonnées des balises de fichiers soient stockées dans le jeu de caractères international UTF-8. Des programmes tels qu'**Ex Falso** (décrit ci-dessus) encodent les métadonnées en UTF-8 par défaut. Si vous avez une archive de fichiers encodés avec des métadonnées dans un jeu de caractères hérité, comme l'encodage cyrillique Windows-1251, vous devez convertir ces fichiers avant de les importer.

Le programme **mid3iconv** (qui fait partie du paquet **python-mutagen** dans Debian et Ubuntu) peut être utilisé pour convertir par lots le jeu de caractères des métadonnées des fichiers sur la ligne de commande. Vous pouvez installer python-mutagen avec la commande sudo apt-get install python-mutagen.

Par exemple, pour prévisualiser la conversion des balises du jeu de caractères Windows-1251 (CP1251) en UTF-8 pour une archive entière de fichiers MP3, vous pouvez utiliser la commande :

```bash
find . -name "*.mp3" -print0 | xargs -0 mid3iconv -e CP1251 -d -p
```

dans le répertoire de base de l'archive. L'option -d spécifie que la nouvelle balise doit être imprimée sur la console du serveur (mode débogage), et l'option -p spécifie une exécution de prévisualisation. Cet aperçu vous permettra de confirmer que les métadonnées sont lues et converties correctement avant d'écrire les nouvelles balises.

Pour convertir réellement toutes les balises et supprimer toute balise ID3v1 présente dans chaque fichier en même temps, vous pouvez utiliser la commande :

```bash
find . -name "*.mp3" -print0 | xargs -0 mid3iconv -e CP1251 --remove-v1
```

Le nom du jeu de caractères original suit l'option **-e**. D'autres jeux de caractères hérités que mid3iconv peut convertir en UTF-8 incluent :

```
KOI8-R: Russian
KOI8-U: Ukrainian

GBK: Traditional Chinese
GB2312: Simplified Chinese

EUC-KR: Korean
EUC-JP: Japanese

CP1253: Greek
CP1254: Turkish
CP1255: Hebrew
CP1256: Arabic
```

## Niveau Audio

Lors de l'acquisition d'un fichier, LibreTime analyse l'intensité sonore de chaque fichier Ogg Vorbis, MP3, AAC ou FLAC, et stocke une valeur ReplayGain pour ce fichier dans sa base de données. Au moment de la lecture, la valeur ReplayGain est fournie à Liquidsoap afin que le gain puisse être automatiquement ajusté pour fournir une sortie moyenne de -14 dBFS (14 décibels sous la pleine échelle). Voir [https://en.wikipedia.org/wiki/ReplayGain](https://en.wikipedia.org/wiki/ReplayGain) pour plus de détails sur ReplayGain.

En raison de cet ajustement automatique du gain, les fichiers dont l'intensité sonore moyenne est supérieure à -14 dBFS ne seront pas plus forts que les fichiers plus silencieux au moment de la lecture, mais le facteur de crête plus faible des fichiers les plus forts (leur rapport crête-moyenne relativement faible) peut être apparent dans la sortie, rendant ces fichiers moins dynamiques. Cela peut être un problème pour la musique populaire contemporaine, qui peut atteindre une moyenne de -9 dBFS ou plus avant le réglage de ReplayGain. (Voir [https://www.soundonsound.com/sound-advice/dynamic-range-loudness-war](https://www.soundonsound.com/sound-advice/dynamic-range-loudness-war) pour une analyse détaillée du problème).

Les producteurs de votre station doivent donc viser un écart de 14 dB entre l'intensité maximale et l'intensité moyenne pour maintenir le facteur de crête de leur matériel préparé (également connu sous le nom de DR14 sur certains mesureurs de gamme dynamique, tels que le T.meter DR14 en ligne de commande disponible sur [https://sourceforge.net/projects/dr14tmeter/)](https://sourceforge.net/projects/dr14tmeter/)). Si les producteurs travaillent avec une norme d'intensité sonore différente, le modificateur ReplayGain de la page Stream Settings de LibreTime peut être ajusté pour s'adapter à leur matériel.

Il convient d'éviter les pics transitoires importants dans les fichiers autrement silencieux, afin de se prémunir contre la nécessité de limiter les pics lorsque ReplayGain est appliqué à ces fichiers plus silencieux.

L'outil de ligne de commande vorbisgain, disponible dans le paquet **vorbisgain** de Debian/Ubuntu, peut être utilisé pour indiquer le ReplayGain d'un fichier Ogg Vorbis individuel avant de l'ingérer dans LibreTime. (Un outil similaire pour les fichiers MP3 est disponible dans le paquet **mp3gain** de Debian/Ubuntu).

Voici un exemple d'un fichier très silencieux où l'utilisation de ReplayGain rendrait la sortie plus forte de 17dB :

```bash
$ vorbisgain -d Peter_Lawson-Three_Gymn.ogg
Analyzing files...

    Gain   | Peak | Scale | New Peak | Track
----------+------+-------+----------+------
+17.39 dB | 4536 |  7.40 |    33585 | Peter_Lawson-Three_Gymn.ogg
```

Et voici un exemple d'un fichier très fort, avec un facteur de crête plus faible, où la sortie sera plus de 7dB plus silencieuse avec ReplayGain appliqué :

```bash
$ vorbisgain -d Snoop_Dogg-Doggfather.ogg
Analyzing files...

   Gain   | Peak  | Scale | New Peak | Track
----------+-------+-------+----------+------
 -7.86 dB | 36592 |  0.40 |    14804 | Snoop_Dogg-Doggfather.ogg
```

Dans la sortie de vorbisgain, Peak est la valeur maximale de l'échantillon du fichier avant l'application de ReplayGain, où une valeur de 32 767 représente la pleine échelle lors du décodage en échantillons 16 bits signés. Notez que les fichiers compressés avec perte peuvent avoir des pics supérieurs à la pleine échelle, en raison d'artefacts d'encodage. La valeur New Peak pour le fichier Snoop Dogg peut être relativement faible en raison de la limitation dure utilisée dans le mastering de ce morceau de musique.

## Silence dans les fichiers multimédias

Avant d'importer des médias, il est bon de vérifier s'il y a des sections silencieuses dans les fichiers médias. Bien que LibreTime compense les silences de début et de fin par l'utilisation de points de repère automatiques, il est préférable de couper ces fichiers à la longueur voulue avant de les télécharger. Deux éditeurs de fichiers audio permettant de supprimer les sections silencieuses sont Audacity et Ocenaudio.

:::danger

Les interruptions de diffusion ou les temps morts peuvent avoir des répercussions juridiques pour votre station. Vérifiez auprès du directeur de votre station ou de l'autorité de communication locale ou nationale pour plus d'informations.

:::

Les introductions silencieuses ou les fondus prolongés peuvent également entraîner des lacunes apparentes dans votre diffusion. Ce phénomène est plus fréquent lors de la lecture de fichiers audio provenant de CD rippés ou copiés à partir de bandes ou de vinyles. Les longues périodes de silence doivent être supprimées des fichiers avant de les télécharger sur LibreTime.

![](./preparing-media-screenshot126-debra_silence.png)
