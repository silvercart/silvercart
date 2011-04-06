<?php

            // write country AD
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AD'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AD";
            $country->ISO3      = "AND";
            $country->FIPS      = "AN";
            $country->ISON      = "200";
            $country->Title     = _t("SilvercartCountry.TITLE_AD");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AE";
            $country->ISO3      = "ARE";
            $country->FIPS      = "AE";
            $country->ISON      = "784";
            $country->Title     = _t("SilvercartCountry.TITLE_AE");
            $country->Continent = "AS";
            $country->Currency  = "AED";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AF";
            $country->ISO3      = "AFG";
            $country->FIPS      = "AF";
            $country->ISON      = "004";
            $country->Title     = _t("SilvercartCountry.TITLE_AF");
            $country->Continent = "AS";
            $country->Currency  = "AFN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AG";
            $country->ISO3      = "ATG";
            $country->FIPS      = "AC";
            $country->ISON      = "028";
            $country->Title     = _t("SilvercartCountry.TITLE_AG");
            $country->Continent = "NA";
            $country->Currency  = "XCD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AI";
            $country->ISO3      = "AIA";
            $country->FIPS      = "AV";
            $country->ISON      = "660";
            $country->Title     = _t("SilvercartCountry.TITLE_AI");
            $country->Continent = "NA";
            $country->Currency  = "XCD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AL";
            $country->ISO3      = "ALB";
            $country->FIPS      = "AL";
            $country->ISON      = "008";
            $country->Title     = _t("SilvercartCountry.TITLE_AL");
            $country->Continent = "EU";
            $country->Currency  = "ALL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AM";
            $country->ISO3      = "ARM";
            $country->FIPS      = "AM";
            $country->ISON      = "051";
            $country->Title     = _t("SilvercartCountry.TITLE_AM");
            $country->Continent = "AS";
            $country->Currency  = "AMD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AN";
            $country->ISO3      = "ANT";
            $country->FIPS      = "NT";
            $country->ISON      = "530";
            $country->Title     = _t("SilvercartCountry.TITLE_AN");
            $country->Continent = "NA";
            $country->Currency  = "ANG";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AO";
            $country->ISO3      = "AGO";
            $country->FIPS      = "AO";
            $country->ISON      = "024";
            $country->Title     = _t("SilvercartCountry.TITLE_AO");
            $country->Continent = "AF";
            $country->Currency  = "AOA";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AQ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AQ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AQ";
            $country->ISO3      = "ATA";
            $country->FIPS      = "AY";
            $country->ISON      = "010";
            $country->Title     = _t("SilvercartCountry.TITLE_AQ");
            $country->Continent = "AN";
            $country->Currency  = "";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AR";
            $country->ISO3      = "ARG";
            $country->FIPS      = "AR";
            $country->ISON      = "032";
            $country->Title     = _t("SilvercartCountry.TITLE_AR");
            $country->Continent = "SA";
            $country->Currency  = "ARS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AS";
            $country->ISO3      = "ASM";
            $country->FIPS      = "AQ";
            $country->ISON      = "016";
            $country->Title     = _t("SilvercartCountry.TITLE_AS");
            $country->Continent = "OC";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AT";
            $country->ISO3      = "AUT";
            $country->FIPS      = "AU";
            $country->ISON      = "040";
            $country->Title     = _t("SilvercartCountry.TITLE_AT");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AU";
            $country->ISO3      = "AUS";
            $country->FIPS      = "AS";
            $country->ISON      = "036";
            $country->Title     = _t("SilvercartCountry.TITLE_AU");
            $country->Continent = "OC";
            $country->Currency  = "AUD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AW";
            $country->ISO3      = "ABW";
            $country->FIPS      = "AA";
            $country->ISON      = "533";
            $country->Title     = _t("SilvercartCountry.TITLE_AW");
            $country->Continent = "NA";
            $country->Currency  = "AWG";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AX
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AX'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AX";
            $country->ISO3      = "ALA";
            $country->FIPS      = "";
            $country->ISON      = "248";
            $country->Title     = _t("SilvercartCountry.TITLE_AX");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country AZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='AZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "AZ";
            $country->ISO3      = "AZE";
            $country->FIPS      = "AJ";
            $country->ISON      = "031";
            $country->Title     = _t("SilvercartCountry.TITLE_AZ");
            $country->Continent = "AS";
            $country->Currency  = "AZN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BA";
            $country->ISO3      = "BIH";
            $country->FIPS      = "BK";
            $country->ISON      = "070";
            $country->Title     = _t("SilvercartCountry.TITLE_BA");
            $country->Continent = "EU";
            $country->Currency  = "BAM";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BB
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BB'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BB";
            $country->ISO3      = "BRB";
            $country->FIPS      = "BB";
            $country->ISON      = "052";
            $country->Title     = _t("SilvercartCountry.TITLE_BB");
            $country->Continent = "NA";
            $country->Currency  = "BBD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BD
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BD'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BD";
            $country->ISO3      = "BGD";
            $country->FIPS      = "BG";
            $country->ISON      = "050";
            $country->Title     = _t("SilvercartCountry.TITLE_BD");
            $country->Continent = "AS";
            $country->Currency  = "BDT";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BE";
            $country->ISO3      = "BEL";
            $country->FIPS      = "BE";
            $country->ISON      = "056";
            $country->Title     = _t("SilvercartCountry.TITLE_BE");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BF";
            $country->ISO3      = "BFA";
            $country->FIPS      = "UV";
            $country->ISON      = "854";
            $country->Title     = _t("SilvercartCountry.TITLE_BF");
            $country->Continent = "AF";
            $country->Currency  = "XOF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BG";
            $country->ISO3      = "BGR";
            $country->FIPS      = "BU";
            $country->ISON      = "100";
            $country->Title     = _t("SilvercartCountry.TITLE_BG");
            $country->Continent = "EU";
            $country->Currency  = "BGN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BH";
            $country->ISO3      = "BHR";
            $country->FIPS      = "BA";
            $country->ISON      = "048";
            $country->Title     = _t("SilvercartCountry.TITLE_BH");
            $country->Continent = "AS";
            $country->Currency  = "BHD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BI";
            $country->ISO3      = "BDI";
            $country->FIPS      = "BY";
            $country->ISON      = "108";
            $country->Title     = _t("SilvercartCountry.TITLE_BI");
            $country->Continent = "AF";
            $country->Currency  = "BIF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BJ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BJ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BJ";
            $country->ISO3      = "BEN";
            $country->FIPS      = "BN";
            $country->ISON      = "204";
            $country->Title     = _t("SilvercartCountry.TITLE_BJ");
            $country->Continent = "AF";
            $country->Currency  = "XOF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BL";
            $country->ISO3      = "BLM";
            $country->FIPS      = "TB";
            $country->ISON      = "652";
            $country->Title     = _t("SilvercartCountry.TITLE_BL");
            $country->Continent = "NA";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BM";
            $country->ISO3      = "BMU";
            $country->FIPS      = "BD";
            $country->ISON      = "060";
            $country->Title     = _t("SilvercartCountry.TITLE_BM");
            $country->Continent = "NA";
            $country->Currency  = "BMD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BN";
            $country->ISO3      = "BRN";
            $country->FIPS      = "BX";
            $country->ISON      = "096";
            $country->Title     = _t("SilvercartCountry.TITLE_BN");
            $country->Continent = "AS";
            $country->Currency  = "BND";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BO";
            $country->ISO3      = "BOL";
            $country->FIPS      = "BL";
            $country->ISON      = "068";
            $country->Title     = _t("SilvercartCountry.TITLE_BO");
            $country->Continent = "SA";
            $country->Currency  = "BOB";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BQ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BQ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BQ";
            $country->ISO3      = "BES";
            $country->FIPS      = "";
            $country->ISON      = "535";
            $country->Title     = _t("SilvercartCountry.TITLE_BQ");
            $country->Continent = "NA";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BR";
            $country->ISO3      = "BRA";
            $country->FIPS      = "BR";
            $country->ISON      = "076";
            $country->Title     = _t("SilvercartCountry.TITLE_BR");
            $country->Continent = "SA";
            $country->Currency  = "BRL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BS";
            $country->ISO3      = "BHS";
            $country->FIPS      = "BF";
            $country->ISON      = "044";
            $country->Title     = _t("SilvercartCountry.TITLE_BS");
            $country->Continent = "NA";
            $country->Currency  = "BSD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BT";
            $country->ISO3      = "BTN";
            $country->FIPS      = "BT";
            $country->ISON      = "064";
            $country->Title     = _t("SilvercartCountry.TITLE_BT");
            $country->Continent = "AS";
            $country->Currency  = "BTN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BV
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BV'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BV";
            $country->ISO3      = "BVT";
            $country->FIPS      = "BV";
            $country->ISON      = "074";
            $country->Title     = _t("SilvercartCountry.TITLE_BV");
            $country->Continent = "AN";
            $country->Currency  = "NOK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BW";
            $country->ISO3      = "BWA";
            $country->FIPS      = "BC";
            $country->ISON      = "072";
            $country->Title     = _t("SilvercartCountry.TITLE_BW");
            $country->Continent = "AF";
            $country->Currency  = "BWP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BY";
            $country->ISO3      = "BLR";
            $country->FIPS      = "BO";
            $country->ISON      = "112";
            $country->Title     = _t("SilvercartCountry.TITLE_BY");
            $country->Continent = "EU";
            $country->Currency  = "BYR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country BZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='BZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "BZ";
            $country->ISO3      = "BLZ";
            $country->FIPS      = "BH";
            $country->ISON      = "084";
            $country->Title     = _t("SilvercartCountry.TITLE_BZ");
            $country->Continent = "NA";
            $country->Currency  = "BZD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CA";
            $country->ISO3      = "CAN";
            $country->FIPS      = "CA";
            $country->ISON      = "124";
            $country->Title     = _t("SilvercartCountry.TITLE_CA");
            $country->Continent = "NA";
            $country->Currency  = "CAD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CC
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CC'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CC";
            $country->ISO3      = "CCK";
            $country->FIPS      = "CK";
            $country->ISON      = "166";
            $country->Title     = _t("SilvercartCountry.TITLE_CC");
            $country->Continent = "AS";
            $country->Currency  = "AUD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CD
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CD'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CD";
            $country->ISO3      = "COD";
            $country->FIPS      = "CG";
            $country->ISON      = "180";
            $country->Title     = _t("SilvercartCountry.TITLE_CD");
            $country->Continent = "AF";
            $country->Currency  = "CDF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CF";
            $country->ISO3      = "CAF";
            $country->FIPS      = "CT";
            $country->ISON      = "140";
            $country->Title     = _t("SilvercartCountry.TITLE_CF");
            $country->Continent = "AF";
            $country->Currency  = "XAF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CG";
            $country->ISO3      = "COG";
            $country->FIPS      = "CF";
            $country->ISON      = "178";
            $country->Title     = _t("SilvercartCountry.TITLE_CG");
            $country->Continent = "AF";
            $country->Currency  = "XAF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CH";
            $country->ISO3      = "CHE";
            $country->FIPS      = "SZ";
            $country->ISON      = "756";
            $country->Title     = _t("SilvercartCountry.TITLE_CH");
            $country->Continent = "EU";
            $country->Currency  = "CHF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CI";
            $country->ISO3      = "CIV";
            $country->FIPS      = "IV";
            $country->ISON      = "384";
            $country->Title     = _t("SilvercartCountry.TITLE_CI");
            $country->Continent = "AF";
            $country->Currency  = "XOF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CK";
            $country->ISO3      = "COK";
            $country->FIPS      = "CW";
            $country->ISON      = "184";
            $country->Title     = _t("SilvercartCountry.TITLE_CK");
            $country->Continent = "OC";
            $country->Currency  = "NZD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CL";
            $country->ISO3      = "CHL";
            $country->FIPS      = "CI";
            $country->ISON      = "152";
            $country->Title     = _t("SilvercartCountry.TITLE_CL");
            $country->Continent = "SA";
            $country->Currency  = "CLP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CM";
            $country->ISO3      = "CMR";
            $country->FIPS      = "CM";
            $country->ISON      = "120";
            $country->Title     = _t("SilvercartCountry.TITLE_CM");
            $country->Continent = "AF";
            $country->Currency  = "XAF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CN";
            $country->ISO3      = "CHN";
            $country->FIPS      = "CH";
            $country->ISON      = "156";
            $country->Title     = _t("SilvercartCountry.TITLE_CN");
            $country->Continent = "AS";
            $country->Currency  = "CNY";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CO";
            $country->ISO3      = "COL";
            $country->FIPS      = "CO";
            $country->ISON      = "170";
            $country->Title     = _t("SilvercartCountry.TITLE_CO");
            $country->Continent = "SA";
            $country->Currency  = "COP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CR";
            $country->ISO3      = "CRI";
            $country->FIPS      = "CS";
            $country->ISON      = "188";
            $country->Title     = _t("SilvercartCountry.TITLE_CR");
            $country->Continent = "NA";
            $country->Currency  = "CRC";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CS";
            $country->ISO3      = "SCG";
            $country->FIPS      = "YI";
            $country->ISON      = "891";
            $country->Title     = _t("SilvercartCountry.TITLE_CS");
            $country->Continent = "EU";
            $country->Currency  = "RSD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CU";
            $country->ISO3      = "CUB";
            $country->FIPS      = "CU";
            $country->ISON      = "192";
            $country->Title     = _t("SilvercartCountry.TITLE_CU");
            $country->Continent = "NA";
            $country->Currency  = "CUP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CV
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CV'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CV";
            $country->ISO3      = "CPV";
            $country->FIPS      = "CV";
            $country->ISON      = "132";
            $country->Title     = _t("SilvercartCountry.TITLE_CV");
            $country->Continent = "AF";
            $country->Currency  = "CVE";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CW";
            $country->ISO3      = "CUW";
            $country->FIPS      = "UC";
            $country->ISON      = "531";
            $country->Title     = _t("SilvercartCountry.TITLE_CW");
            $country->Continent = "NA";
            $country->Currency  = "ANG";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CX
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CX'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CX";
            $country->ISO3      = "CXR";
            $country->FIPS      = "KT";
            $country->ISON      = "162";
            $country->Title     = _t("SilvercartCountry.TITLE_CX");
            $country->Continent = "AS";
            $country->Currency  = "AUD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CY";
            $country->ISO3      = "CYP";
            $country->FIPS      = "CY";
            $country->ISON      = "196";
            $country->Title     = _t("SilvercartCountry.TITLE_CY");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country CZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='CZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "CZ";
            $country->ISO3      = "CZE";
            $country->FIPS      = "EZ";
            $country->ISON      = "203";
            $country->Title     = _t("SilvercartCountry.TITLE_CZ");
            $country->Continent = "EU";
            $country->Currency  = "CZK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country DE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='DE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "DE";
            $country->ISO3      = "DEU";
            $country->FIPS      = "GM";
            $country->ISON      = "276";
            $country->Title     = _t("SilvercartCountry.TITLE_DE");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country DJ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='DJ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "DJ";
            $country->ISO3      = "DJI";
            $country->FIPS      = "DJ";
            $country->ISON      = "262";
            $country->Title     = _t("SilvercartCountry.TITLE_DJ");
            $country->Continent = "AF";
            $country->Currency  = "DJF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country DK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='DK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "DK";
            $country->ISO3      = "DNK";
            $country->FIPS      = "DA";
            $country->ISON      = "208";
            $country->Title     = _t("SilvercartCountry.TITLE_DK");
            $country->Continent = "EU";
            $country->Currency  = "DKK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country DM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='DM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "DM";
            $country->ISO3      = "DMA";
            $country->FIPS      = "DO";
            $country->ISON      = "212";
            $country->Title     = _t("SilvercartCountry.TITLE_DM");
            $country->Continent = "NA";
            $country->Currency  = "XCD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country DO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='DO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "DO";
            $country->ISO3      = "DOM";
            $country->FIPS      = "DR";
            $country->ISON      = "214";
            $country->Title     = _t("SilvercartCountry.TITLE_DO");
            $country->Continent = "NA";
            $country->Currency  = "DOP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country DZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='DZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "DZ";
            $country->ISO3      = "DZA";
            $country->FIPS      = "AG";
            $country->ISON      = "012";
            $country->Title     = _t("SilvercartCountry.TITLE_DZ");
            $country->Continent = "AF";
            $country->Currency  = "DZD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country EC
        if (!DataObject::get('SilvercartCountry',"`ISO2`='EC'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "EC";
            $country->ISO3      = "ECU";
            $country->FIPS      = "EC";
            $country->ISON      = "218";
            $country->Title     = _t("SilvercartCountry.TITLE_EC");
            $country->Continent = "SA";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country EE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='EE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "EE";
            $country->ISO3      = "EST";
            $country->FIPS      = "EN";
            $country->ISON      = "233";
            $country->Title     = _t("SilvercartCountry.TITLE_EE");
            $country->Continent = "EU";
            $country->Currency  = "EEK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country EG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='EG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "EG";
            $country->ISO3      = "EGY";
            $country->FIPS      = "EG";
            $country->ISON      = "818";
            $country->Title     = _t("SilvercartCountry.TITLE_EG");
            $country->Continent = "AF";
            $country->Currency  = "EGP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country EH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='EH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "EH";
            $country->ISO3      = "ESH";
            $country->FIPS      = "WI";
            $country->ISON      = "732";
            $country->Title     = _t("SilvercartCountry.TITLE_EH");
            $country->Continent = "AF";
            $country->Currency  = "MAD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ER
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ER'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ER";
            $country->ISO3      = "ERI";
            $country->FIPS      = "ER";
            $country->ISON      = "232";
            $country->Title     = _t("SilvercartCountry.TITLE_ER");
            $country->Continent = "AF";
            $country->Currency  = "ERN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ES
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ES'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ES";
            $country->ISO3      = "ESP";
            $country->FIPS      = "SP";
            $country->ISON      = "724";
            $country->Title     = _t("SilvercartCountry.TITLE_ES");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ET
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ET'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ET";
            $country->ISO3      = "ETH";
            $country->FIPS      = "ET";
            $country->ISON      = "231";
            $country->Title     = _t("SilvercartCountry.TITLE_ET");
            $country->Continent = "AF";
            $country->Currency  = "ETB";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country FI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='FI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "FI";
            $country->ISO3      = "FIN";
            $country->FIPS      = "FI";
            $country->ISON      = "246";
            $country->Title     = _t("SilvercartCountry.TITLE_FI");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country FJ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='FJ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "FJ";
            $country->ISO3      = "FJI";
            $country->FIPS      = "FJ";
            $country->ISON      = "242";
            $country->Title     = _t("SilvercartCountry.TITLE_FJ");
            $country->Continent = "OC";
            $country->Currency  = "FJD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country FK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='FK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "FK";
            $country->ISO3      = "FLK";
            $country->FIPS      = "FK";
            $country->ISON      = "238";
            $country->Title     = _t("SilvercartCountry.TITLE_FK");
            $country->Continent = "SA";
            $country->Currency  = "FKP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country FM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='FM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "FM";
            $country->ISO3      = "FSM";
            $country->FIPS      = "FM";
            $country->ISON      = "583";
            $country->Title     = _t("SilvercartCountry.TITLE_FM");
            $country->Continent = "OC";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country FO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='FO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "FO";
            $country->ISO3      = "FRO";
            $country->FIPS      = "FO";
            $country->ISON      = "234";
            $country->Title     = _t("SilvercartCountry.TITLE_FO");
            $country->Continent = "EU";
            $country->Currency  = "DKK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country FR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='FR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "FR";
            $country->ISO3      = "FRA";
            $country->FIPS      = "FR";
            $country->ISON      = "250";
            $country->Title     = _t("SilvercartCountry.TITLE_FR");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GA";
            $country->ISO3      = "GAB";
            $country->FIPS      = "GB";
            $country->ISON      = "266";
            $country->Title     = _t("SilvercartCountry.TITLE_GA");
            $country->Continent = "AF";
            $country->Currency  = "XAF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GB
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GB'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GB";
            $country->ISO3      = "GBR";
            $country->FIPS      = "UK";
            $country->ISON      = "826";
            $country->Title     = _t("SilvercartCountry.TITLE_GB");
            $country->Continent = "EU";
            $country->Currency  = "GBP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GD
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GD'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GD";
            $country->ISO3      = "GRD";
            $country->FIPS      = "GJ";
            $country->ISON      = "308";
            $country->Title     = _t("SilvercartCountry.TITLE_GD");
            $country->Continent = "NA";
            $country->Currency  = "XCD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GE";
            $country->ISO3      = "GEO";
            $country->FIPS      = "GG";
            $country->ISON      = "268";
            $country->Title     = _t("SilvercartCountry.TITLE_GE");
            $country->Continent = "AS";
            $country->Currency  = "GEL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GF";
            $country->ISO3      = "GUF";
            $country->FIPS      = "FG";
            $country->ISON      = "254";
            $country->Title     = _t("SilvercartCountry.TITLE_GF");
            $country->Continent = "SA";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GG";
            $country->ISO3      = "GGY";
            $country->FIPS      = "GK";
            $country->ISON      = "831";
            $country->Title     = _t("SilvercartCountry.TITLE_GG");
            $country->Continent = "EU";
            $country->Currency  = "GBP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GH";
            $country->ISO3      = "GHA";
            $country->FIPS      = "GH";
            $country->ISON      = "288";
            $country->Title     = _t("SilvercartCountry.TITLE_GH");
            $country->Continent = "AF";
            $country->Currency  = "GHS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GI";
            $country->ISO3      = "GIB";
            $country->FIPS      = "GI";
            $country->ISON      = "292";
            $country->Title     = _t("SilvercartCountry.TITLE_GI");
            $country->Continent = "EU";
            $country->Currency  = "GIP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GL";
            $country->ISO3      = "GRL";
            $country->FIPS      = "GL";
            $country->ISON      = "304";
            $country->Title     = _t("SilvercartCountry.TITLE_GL");
            $country->Continent = "NA";
            $country->Currency  = "DKK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GM";
            $country->ISO3      = "GMB";
            $country->FIPS      = "GA";
            $country->ISON      = "270";
            $country->Title     = _t("SilvercartCountry.TITLE_GM");
            $country->Continent = "AF";
            $country->Currency  = "GMD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GN";
            $country->ISO3      = "GIN";
            $country->FIPS      = "GV";
            $country->ISON      = "324";
            $country->Title     = _t("SilvercartCountry.TITLE_GN");
            $country->Continent = "AF";
            $country->Currency  = "GNF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GP
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GP'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GP";
            $country->ISO3      = "GLP";
            $country->FIPS      = "GP";
            $country->ISON      = "312";
            $country->Title     = _t("SilvercartCountry.TITLE_GP");
            $country->Continent = "NA";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GQ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GQ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GQ";
            $country->ISO3      = "GNQ";
            $country->FIPS      = "EK";
            $country->ISON      = "226";
            $country->Title     = _t("SilvercartCountry.TITLE_GQ");
            $country->Continent = "AF";
            $country->Currency  = "XAF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GR";
            $country->ISO3      = "GRC";
            $country->FIPS      = "GR";
            $country->ISON      = "300";
            $country->Title     = _t("SilvercartCountry.TITLE_GR");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GS";
            $country->ISO3      = "SGS";
            $country->FIPS      = "SX";
            $country->ISON      = "239";
            $country->Title     = _t("SilvercartCountry.TITLE_GS");
            $country->Continent = "AN";
            $country->Currency  = "GBP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GT";
            $country->ISO3      = "GTM";
            $country->FIPS      = "GT";
            $country->ISON      = "320";
            $country->Title     = _t("SilvercartCountry.TITLE_GT");
            $country->Continent = "NA";
            $country->Currency  = "GTQ";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GU";
            $country->ISO3      = "GUM";
            $country->FIPS      = "GQ";
            $country->ISON      = "316";
            $country->Title     = _t("SilvercartCountry.TITLE_GU");
            $country->Continent = "OC";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GW";
            $country->ISO3      = "GNB";
            $country->FIPS      = "PU";
            $country->ISON      = "624";
            $country->Title     = _t("SilvercartCountry.TITLE_GW");
            $country->Continent = "AF";
            $country->Currency  = "XOF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country GY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='GY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "GY";
            $country->ISO3      = "GUY";
            $country->FIPS      = "GY";
            $country->ISON      = "328";
            $country->Title     = _t("SilvercartCountry.TITLE_GY");
            $country->Continent = "SA";
            $country->Currency  = "GYD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country HK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='HK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "HK";
            $country->ISO3      = "HKG";
            $country->FIPS      = "HK";
            $country->ISON      = "344";
            $country->Title     = _t("SilvercartCountry.TITLE_HK");
            $country->Continent = "AS";
            $country->Currency  = "HKD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country HM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='HM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "HM";
            $country->ISO3      = "HMD";
            $country->FIPS      = "HM";
            $country->ISON      = "334";
            $country->Title     = _t("SilvercartCountry.TITLE_HM");
            $country->Continent = "AN";
            $country->Currency  = "AUD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country HN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='HN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "HN";
            $country->ISO3      = "HND";
            $country->FIPS      = "HO";
            $country->ISON      = "340";
            $country->Title     = _t("SilvercartCountry.TITLE_HN");
            $country->Continent = "NA";
            $country->Currency  = "HNL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country HR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='HR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "HR";
            $country->ISO3      = "HRV";
            $country->FIPS      = "HR";
            $country->ISON      = "191";
            $country->Title     = _t("SilvercartCountry.TITLE_HR");
            $country->Continent = "EU";
            $country->Currency  = "HRK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country HT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='HT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "HT";
            $country->ISO3      = "HTI";
            $country->FIPS      = "HA";
            $country->ISON      = "332";
            $country->Title     = _t("SilvercartCountry.TITLE_HT");
            $country->Continent = "NA";
            $country->Currency  = "HTG";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country HU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='HU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "HU";
            $country->ISO3      = "HUN";
            $country->FIPS      = "HU";
            $country->ISON      = "348";
            $country->Title     = _t("SilvercartCountry.TITLE_HU");
            $country->Continent = "EU";
            $country->Currency  = "HUF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ID
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ID'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ID";
            $country->ISO3      = "IDN";
            $country->FIPS      = "ID";
            $country->ISON      = "360";
            $country->Title     = _t("SilvercartCountry.TITLE_ID");
            $country->Continent = "AS";
            $country->Currency  = "IDR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IE";
            $country->ISO3      = "IRL";
            $country->FIPS      = "EI";
            $country->ISON      = "372";
            $country->Title     = _t("SilvercartCountry.TITLE_IE");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IL";
            $country->ISO3      = "ISR";
            $country->FIPS      = "IS";
            $country->ISON      = "376";
            $country->Title     = _t("SilvercartCountry.TITLE_IL");
            $country->Continent = "AS";
            $country->Currency  = "ILS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IM";
            $country->ISO3      = "IMN";
            $country->FIPS      = "IM";
            $country->ISON      = "833";
            $country->Title     = _t("SilvercartCountry.TITLE_IM");
            $country->Continent = "EU";
            $country->Currency  = "GBP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IN";
            $country->ISO3      = "IND";
            $country->FIPS      = "IN";
            $country->ISON      = "356";
            $country->Title     = _t("SilvercartCountry.TITLE_IN");
            $country->Continent = "AS";
            $country->Currency  = "INR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IO";
            $country->ISO3      = "IOT";
            $country->FIPS      = "IO";
            $country->ISON      = "086";
            $country->Title     = _t("SilvercartCountry.TITLE_IO");
            $country->Continent = "AS";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IQ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IQ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IQ";
            $country->ISO3      = "IRQ";
            $country->FIPS      = "IZ";
            $country->ISON      = "368";
            $country->Title     = _t("SilvercartCountry.TITLE_IQ");
            $country->Continent = "AS";
            $country->Currency  = "IQD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IR";
            $country->ISO3      = "IRN";
            $country->FIPS      = "IR";
            $country->ISON      = "364";
            $country->Title     = _t("SilvercartCountry.TITLE_IR");
            $country->Continent = "AS";
            $country->Currency  = "IRR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IS";
            $country->ISO3      = "ISL";
            $country->FIPS      = "IC";
            $country->ISON      = "352";
            $country->Title     = _t("SilvercartCountry.TITLE_IS");
            $country->Continent = "EU";
            $country->Currency  = "ISK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country IT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='IT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "IT";
            $country->ISO3      = "ITA";
            $country->FIPS      = "IT";
            $country->ISON      = "380";
            $country->Title     = _t("SilvercartCountry.TITLE_IT");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country JE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='JE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "JE";
            $country->ISO3      = "JEY";
            $country->FIPS      = "JE";
            $country->ISON      = "832";
            $country->Title     = _t("SilvercartCountry.TITLE_JE");
            $country->Continent = "EU";
            $country->Currency  = "GBP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country JM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='JM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "JM";
            $country->ISO3      = "JAM";
            $country->FIPS      = "JM";
            $country->ISON      = "388";
            $country->Title     = _t("SilvercartCountry.TITLE_JM");
            $country->Continent = "NA";
            $country->Currency  = "JMD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country JO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='JO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "JO";
            $country->ISO3      = "JOR";
            $country->FIPS      = "JO";
            $country->ISON      = "400";
            $country->Title     = _t("SilvercartCountry.TITLE_JO");
            $country->Continent = "AS";
            $country->Currency  = "JOD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country JP
        if (!DataObject::get('SilvercartCountry',"`ISO2`='JP'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "JP";
            $country->ISO3      = "JPN";
            $country->FIPS      = "JA";
            $country->ISON      = "392";
            $country->Title     = _t("SilvercartCountry.TITLE_JP");
            $country->Continent = "AS";
            $country->Currency  = "JPY";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KE";
            $country->ISO3      = "KEN";
            $country->FIPS      = "KE";
            $country->ISON      = "404";
            $country->Title     = _t("SilvercartCountry.TITLE_KE");
            $country->Continent = "AF";
            $country->Currency  = "KES";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KG";
            $country->ISO3      = "KGZ";
            $country->FIPS      = "KG";
            $country->ISON      = "417";
            $country->Title     = _t("SilvercartCountry.TITLE_KG");
            $country->Continent = "AS";
            $country->Currency  = "KGS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KH";
            $country->ISO3      = "KHM";
            $country->FIPS      = "CB";
            $country->ISON      = "116";
            $country->Title     = _t("SilvercartCountry.TITLE_KH");
            $country->Continent = "AS";
            $country->Currency  = "KHR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KI";
            $country->ISO3      = "KIR";
            $country->FIPS      = "KR";
            $country->ISON      = "296";
            $country->Title     = _t("SilvercartCountry.TITLE_KI");
            $country->Continent = "OC";
            $country->Currency  = "AUD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KM";
            $country->ISO3      = "COM";
            $country->FIPS      = "CN";
            $country->ISON      = "174";
            $country->Title     = _t("SilvercartCountry.TITLE_KM");
            $country->Continent = "AF";
            $country->Currency  = "KMF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KN";
            $country->ISO3      = "KNA";
            $country->FIPS      = "SC";
            $country->ISON      = "659";
            $country->Title     = _t("SilvercartCountry.TITLE_KN");
            $country->Continent = "NA";
            $country->Currency  = "XCD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KP
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KP'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KP";
            $country->ISO3      = "PRK";
            $country->FIPS      = "KN";
            $country->ISON      = "408";
            $country->Title     = _t("SilvercartCountry.TITLE_KP");
            $country->Continent = "AS";
            $country->Currency  = "KPW";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KR";
            $country->ISO3      = "KOR";
            $country->FIPS      = "KS";
            $country->ISON      = "410";
            $country->Title     = _t("SilvercartCountry.TITLE_KR");
            $country->Continent = "AS";
            $country->Currency  = "KRW";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KW";
            $country->ISO3      = "KWT";
            $country->FIPS      = "KU";
            $country->ISON      = "414";
            $country->Title     = _t("SilvercartCountry.TITLE_KW");
            $country->Continent = "AS";
            $country->Currency  = "KWD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KY";
            $country->ISO3      = "CYM";
            $country->FIPS      = "CJ";
            $country->ISON      = "136";
            $country->Title     = _t("SilvercartCountry.TITLE_KY");
            $country->Continent = "NA";
            $country->Currency  = "KYD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country KZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='KZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "KZ";
            $country->ISO3      = "KAZ";
            $country->FIPS      = "KZ";
            $country->ISON      = "398";
            $country->Title     = _t("SilvercartCountry.TITLE_KZ");
            $country->Continent = "AS";
            $country->Currency  = "KZT";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LA";
            $country->ISO3      = "LAO";
            $country->FIPS      = "LA";
            $country->ISON      = "418";
            $country->Title     = _t("SilvercartCountry.TITLE_LA");
            $country->Continent = "AS";
            $country->Currency  = "LAK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LB
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LB'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LB";
            $country->ISO3      = "LBN";
            $country->FIPS      = "LE";
            $country->ISON      = "422";
            $country->Title     = _t("SilvercartCountry.TITLE_LB");
            $country->Continent = "AS";
            $country->Currency  = "LBP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LC
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LC'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LC";
            $country->ISO3      = "LCA";
            $country->FIPS      = "ST";
            $country->ISON      = "662";
            $country->Title     = _t("SilvercartCountry.TITLE_LC");
            $country->Continent = "NA";
            $country->Currency  = "XCD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LI";
            $country->ISO3      = "LIE";
            $country->FIPS      = "LS";
            $country->ISON      = "438";
            $country->Title     = _t("SilvercartCountry.TITLE_LI");
            $country->Continent = "EU";
            $country->Currency  = "CHF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LK";
            $country->ISO3      = "LKA";
            $country->FIPS      = "CE";
            $country->ISON      = "144";
            $country->Title     = _t("SilvercartCountry.TITLE_LK");
            $country->Continent = "AS";
            $country->Currency  = "LKR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LR";
            $country->ISO3      = "LBR";
            $country->FIPS      = "LI";
            $country->ISON      = "430";
            $country->Title     = _t("SilvercartCountry.TITLE_LR");
            $country->Continent = "AF";
            $country->Currency  = "LRD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LS";
            $country->ISO3      = "LSO";
            $country->FIPS      = "LT";
            $country->ISON      = "426";
            $country->Title     = _t("SilvercartCountry.TITLE_LS");
            $country->Continent = "AF";
            $country->Currency  = "LSL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LT";
            $country->ISO3      = "LTU";
            $country->FIPS      = "LH";
            $country->ISON      = "440";
            $country->Title     = _t("SilvercartCountry.TITLE_LT");
            $country->Continent = "EU";
            $country->Currency  = "LTL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LU";
            $country->ISO3      = "LUX";
            $country->FIPS      = "LU";
            $country->ISON      = "442";
            $country->Title     = _t("SilvercartCountry.TITLE_LU");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LV
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LV'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LV";
            $country->ISO3      = "LVA";
            $country->FIPS      = "LG";
            $country->ISON      = "428";
            $country->Title     = _t("SilvercartCountry.TITLE_LV");
            $country->Continent = "EU";
            $country->Currency  = "LVL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country LY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='LY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "LY";
            $country->ISO3      = "LBY";
            $country->FIPS      = "LY";
            $country->ISON      = "434";
            $country->Title     = _t("SilvercartCountry.TITLE_LY");
            $country->Continent = "AF";
            $country->Currency  = "LYD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MA";
            $country->ISO3      = "MAR";
            $country->FIPS      = "MO";
            $country->ISON      = "504";
            $country->Title     = _t("SilvercartCountry.TITLE_MA");
            $country->Continent = "AF";
            $country->Currency  = "MAD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MC
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MC'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MC";
            $country->ISO3      = "MCO";
            $country->FIPS      = "MN";
            $country->ISON      = "492";
            $country->Title     = _t("SilvercartCountry.TITLE_MC");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MD
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MD'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MD";
            $country->ISO3      = "MDA";
            $country->FIPS      = "MD";
            $country->ISON      = "498";
            $country->Title     = _t("SilvercartCountry.TITLE_MD");
            $country->Continent = "EU";
            $country->Currency  = "MDL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ME
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ME'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ME";
            $country->ISO3      = "MNE";
            $country->FIPS      = "MJ";
            $country->ISON      = "499";
            $country->Title     = _t("SilvercartCountry.TITLE_ME");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MF";
            $country->ISO3      = "MAF";
            $country->FIPS      = "RN";
            $country->ISON      = "663";
            $country->Title     = _t("SilvercartCountry.TITLE_MF");
            $country->Continent = "NA";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MG";
            $country->ISO3      = "MDG";
            $country->FIPS      = "MA";
            $country->ISON      = "450";
            $country->Title     = _t("SilvercartCountry.TITLE_MG");
            $country->Continent = "AF";
            $country->Currency  = "MGA";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MH";
            $country->ISO3      = "MHL";
            $country->FIPS      = "RM";
            $country->ISON      = "584";
            $country->Title     = _t("SilvercartCountry.TITLE_MH");
            $country->Continent = "OC";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MK";
            $country->ISO3      = "MKD";
            $country->FIPS      = "MK";
            $country->ISON      = "807";
            $country->Title     = _t("SilvercartCountry.TITLE_MK");
            $country->Continent = "EU";
            $country->Currency  = "MKD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ML
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ML'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ML";
            $country->ISO3      = "MLI";
            $country->FIPS      = "ML";
            $country->ISON      = "466";
            $country->Title     = _t("SilvercartCountry.TITLE_ML");
            $country->Continent = "AF";
            $country->Currency  = "XOF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MM";
            $country->ISO3      = "MMR";
            $country->FIPS      = "BM";
            $country->ISON      = "104";
            $country->Title     = _t("SilvercartCountry.TITLE_MM");
            $country->Continent = "AS";
            $country->Currency  = "MMK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MN";
            $country->ISO3      = "MNG";
            $country->FIPS      = "MG";
            $country->ISON      = "496";
            $country->Title     = _t("SilvercartCountry.TITLE_MN");
            $country->Continent = "AS";
            $country->Currency  = "MNT";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MO";
            $country->ISO3      = "MAC";
            $country->FIPS      = "MC";
            $country->ISON      = "446";
            $country->Title     = _t("SilvercartCountry.TITLE_MO");
            $country->Continent = "AS";
            $country->Currency  = "MOP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MP
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MP'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MP";
            $country->ISO3      = "MNP";
            $country->FIPS      = "CQ";
            $country->ISON      = "580";
            $country->Title     = _t("SilvercartCountry.TITLE_MP");
            $country->Continent = "OC";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MQ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MQ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MQ";
            $country->ISO3      = "MTQ";
            $country->FIPS      = "MB";
            $country->ISON      = "474";
            $country->Title     = _t("SilvercartCountry.TITLE_MQ");
            $country->Continent = "NA";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MR";
            $country->ISO3      = "MRT";
            $country->FIPS      = "MR";
            $country->ISON      = "478";
            $country->Title     = _t("SilvercartCountry.TITLE_MR");
            $country->Continent = "AF";
            $country->Currency  = "MRO";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MS";
            $country->ISO3      = "MSR";
            $country->FIPS      = "MH";
            $country->ISON      = "500";
            $country->Title     = _t("SilvercartCountry.TITLE_MS");
            $country->Continent = "NA";
            $country->Currency  = "XCD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MT";
            $country->ISO3      = "MLT";
            $country->FIPS      = "MT";
            $country->ISON      = "470";
            $country->Title     = _t("SilvercartCountry.TITLE_MT");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MU";
            $country->ISO3      = "MUS";
            $country->FIPS      = "MP";
            $country->ISON      = "480";
            $country->Title     = _t("SilvercartCountry.TITLE_MU");
            $country->Continent = "AF";
            $country->Currency  = "MUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MV
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MV'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MV";
            $country->ISO3      = "MDV";
            $country->FIPS      = "MV";
            $country->ISON      = "462";
            $country->Title     = _t("SilvercartCountry.TITLE_MV");
            $country->Continent = "AS";
            $country->Currency  = "MVR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MW";
            $country->ISO3      = "MWI";
            $country->FIPS      = "MI";
            $country->ISON      = "454";
            $country->Title     = _t("SilvercartCountry.TITLE_MW");
            $country->Continent = "AF";
            $country->Currency  = "MWK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MX
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MX'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MX";
            $country->ISO3      = "MEX";
            $country->FIPS      = "MX";
            $country->ISON      = "484";
            $country->Title     = _t("SilvercartCountry.TITLE_MX");
            $country->Continent = "NA";
            $country->Currency  = "MXN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MY";
            $country->ISO3      = "MYS";
            $country->FIPS      = "MY";
            $country->ISON      = "458";
            $country->Title     = _t("SilvercartCountry.TITLE_MY");
            $country->Continent = "AS";
            $country->Currency  = "MYR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country MZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='MZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "MZ";
            $country->ISO3      = "MOZ";
            $country->FIPS      = "MZ";
            $country->ISON      = "508";
            $country->Title     = _t("SilvercartCountry.TITLE_MZ");
            $country->Continent = "AF";
            $country->Currency  = "MZN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NA";
            $country->ISO3      = "NAM";
            $country->FIPS      = "WA";
            $country->ISON      = "516";
            $country->Title     = _t("SilvercartCountry.TITLE_NA");
            $country->Continent = "AF";
            $country->Currency  = "NAD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NC
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NC'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NC";
            $country->ISO3      = "NCL";
            $country->FIPS      = "NC";
            $country->ISON      = "540";
            $country->Title     = _t("SilvercartCountry.TITLE_NC");
            $country->Continent = "OC";
            $country->Currency  = "XPF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NE";
            $country->ISO3      = "NER";
            $country->FIPS      = "NG";
            $country->ISON      = "562";
            $country->Title     = _t("SilvercartCountry.TITLE_NE");
            $country->Continent = "AF";
            $country->Currency  = "XOF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NF";
            $country->ISO3      = "NFK";
            $country->FIPS      = "NF";
            $country->ISON      = "574";
            $country->Title     = _t("SilvercartCountry.TITLE_NF");
            $country->Continent = "OC";
            $country->Currency  = "AUD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NG";
            $country->ISO3      = "NGA";
            $country->FIPS      = "NI";
            $country->ISON      = "566";
            $country->Title     = _t("SilvercartCountry.TITLE_NG");
            $country->Continent = "AF";
            $country->Currency  = "NGN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NI";
            $country->ISO3      = "NIC";
            $country->FIPS      = "NU";
            $country->ISON      = "558";
            $country->Title     = _t("SilvercartCountry.TITLE_NI");
            $country->Continent = "NA";
            $country->Currency  = "NIO";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NL";
            $country->ISO3      = "NLD";
            $country->FIPS      = "NL";
            $country->ISON      = "528";
            $country->Title     = _t("SilvercartCountry.TITLE_NL");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NO";
            $country->ISO3      = "NOR";
            $country->FIPS      = "NO";
            $country->ISON      = "578";
            $country->Title     = _t("SilvercartCountry.TITLE_NO");
            $country->Continent = "EU";
            $country->Currency  = "NOK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NP
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NP'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NP";
            $country->ISO3      = "NPL";
            $country->FIPS      = "NP";
            $country->ISON      = "524";
            $country->Title     = _t("SilvercartCountry.TITLE_NP");
            $country->Continent = "AS";
            $country->Currency  = "NPR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NR";
            $country->ISO3      = "NRU";
            $country->FIPS      = "NR";
            $country->ISON      = "520";
            $country->Title     = _t("SilvercartCountry.TITLE_NR");
            $country->Continent = "OC";
            $country->Currency  = "AUD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NU";
            $country->ISO3      = "NIU";
            $country->FIPS      = "NE";
            $country->ISON      = "570";
            $country->Title     = _t("SilvercartCountry.TITLE_NU");
            $country->Continent = "OC";
            $country->Currency  = "NZD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country NZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='NZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "NZ";
            $country->ISO3      = "NZL";
            $country->FIPS      = "NZ";
            $country->ISON      = "554";
            $country->Title     = _t("SilvercartCountry.TITLE_NZ");
            $country->Continent = "OC";
            $country->Currency  = "NZD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country OM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='OM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "OM";
            $country->ISO3      = "OMN";
            $country->FIPS      = "MU";
            $country->ISON      = "512";
            $country->Title     = _t("SilvercartCountry.TITLE_OM");
            $country->Continent = "AS";
            $country->Currency  = "OMR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PA";
            $country->ISO3      = "PAN";
            $country->FIPS      = "PM";
            $country->ISON      = "591";
            $country->Title     = _t("SilvercartCountry.TITLE_PA");
            $country->Continent = "NA";
            $country->Currency  = "PAB";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PE";
            $country->ISO3      = "PER";
            $country->FIPS      = "PE";
            $country->ISON      = "604";
            $country->Title     = _t("SilvercartCountry.TITLE_PE");
            $country->Continent = "SA";
            $country->Currency  = "PEN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PF";
            $country->ISO3      = "PYF";
            $country->FIPS      = "FP";
            $country->ISON      = "258";
            $country->Title     = _t("SilvercartCountry.TITLE_PF");
            $country->Continent = "OC";
            $country->Currency  = "XPF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PG";
            $country->ISO3      = "PNG";
            $country->FIPS      = "PP";
            $country->ISON      = "598";
            $country->Title     = _t("SilvercartCountry.TITLE_PG");
            $country->Continent = "OC";
            $country->Currency  = "PGK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PH";
            $country->ISO3      = "PHL";
            $country->FIPS      = "RP";
            $country->ISON      = "608";
            $country->Title     = _t("SilvercartCountry.TITLE_PH");
            $country->Continent = "AS";
            $country->Currency  = "PHP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PK";
            $country->ISO3      = "PAK";
            $country->FIPS      = "PK";
            $country->ISON      = "586";
            $country->Title     = _t("SilvercartCountry.TITLE_PK");
            $country->Continent = "AS";
            $country->Currency  = "PKR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PL";
            $country->ISO3      = "POL";
            $country->FIPS      = "PL";
            $country->ISON      = "616";
            $country->Title     = _t("SilvercartCountry.TITLE_PL");
            $country->Continent = "EU";
            $country->Currency  = "PLN";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PM";
            $country->ISO3      = "SPM";
            $country->FIPS      = "SB";
            $country->ISON      = "666";
            $country->Title     = _t("SilvercartCountry.TITLE_PM");
            $country->Continent = "NA";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PN";
            $country->ISO3      = "PCN";
            $country->FIPS      = "PC";
            $country->ISON      = "612";
            $country->Title     = _t("SilvercartCountry.TITLE_PN");
            $country->Continent = "OC";
            $country->Currency  = "NZD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PR";
            $country->ISO3      = "PRI";
            $country->FIPS      = "RQ";
            $country->ISON      = "630";
            $country->Title     = _t("SilvercartCountry.TITLE_PR");
            $country->Continent = "NA";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PS";
            $country->ISO3      = "PSE";
            $country->FIPS      = "WE";
            $country->ISON      = "275";
            $country->Title     = _t("SilvercartCountry.TITLE_PS");
            $country->Continent = "AS";
            $country->Currency  = "ILS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PT";
            $country->ISO3      = "PRT";
            $country->FIPS      = "PO";
            $country->ISON      = "620";
            $country->Title     = _t("SilvercartCountry.TITLE_PT");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PW";
            $country->ISO3      = "PLW";
            $country->FIPS      = "PS";
            $country->ISON      = "585";
            $country->Title     = _t("SilvercartCountry.TITLE_PW");
            $country->Continent = "OC";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country PY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='PY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "PY";
            $country->ISO3      = "PRY";
            $country->FIPS      = "PA";
            $country->ISON      = "600";
            $country->Title     = _t("SilvercartCountry.TITLE_PY");
            $country->Continent = "SA";
            $country->Currency  = "PYG";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country QA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='QA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "QA";
            $country->ISO3      = "QAT";
            $country->FIPS      = "QA";
            $country->ISON      = "634";
            $country->Title     = _t("SilvercartCountry.TITLE_QA");
            $country->Continent = "AS";
            $country->Currency  = "QAR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country RE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='RE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "RE";
            $country->ISO3      = "REU";
            $country->FIPS      = "RE";
            $country->ISON      = "638";
            $country->Title     = _t("SilvercartCountry.TITLE_RE");
            $country->Continent = "AF";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country RO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='RO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "RO";
            $country->ISO3      = "ROU";
            $country->FIPS      = "RO";
            $country->ISON      = "642";
            $country->Title     = _t("SilvercartCountry.TITLE_RO");
            $country->Continent = "EU";
            $country->Currency  = "RON";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country RS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='RS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "RS";
            $country->ISO3      = "SRB";
            $country->FIPS      = "RI";
            $country->ISON      = "688";
            $country->Title     = _t("SilvercartCountry.TITLE_RS");
            $country->Continent = "EU";
            $country->Currency  = "RSD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country RU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='RU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "RU";
            $country->ISO3      = "RUS";
            $country->FIPS      = "RS";
            $country->ISON      = "643";
            $country->Title     = _t("SilvercartCountry.TITLE_RU");
            $country->Continent = "EU";
            $country->Currency  = "RUB";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country RW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='RW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "RW";
            $country->ISO3      = "RWA";
            $country->FIPS      = "RW";
            $country->ISON      = "646";
            $country->Title     = _t("SilvercartCountry.TITLE_RW");
            $country->Continent = "AF";
            $country->Currency  = "RWF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SA";
            $country->ISO3      = "SAU";
            $country->FIPS      = "SA";
            $country->ISON      = "682";
            $country->Title     = _t("SilvercartCountry.TITLE_SA");
            $country->Continent = "AS";
            $country->Currency  = "SAR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SB
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SB'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SB";
            $country->ISO3      = "SLB";
            $country->FIPS      = "BP";
            $country->ISON      = "090";
            $country->Title     = _t("SilvercartCountry.TITLE_SB");
            $country->Continent = "OC";
            $country->Currency  = "SBD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SC
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SC'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SC";
            $country->ISO3      = "SYC";
            $country->FIPS      = "SE";
            $country->ISON      = "690";
            $country->Title     = _t("SilvercartCountry.TITLE_SC");
            $country->Continent = "AF";
            $country->Currency  = "SCR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SD
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SD'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SD";
            $country->ISO3      = "SDN";
            $country->FIPS      = "SU";
            $country->ISON      = "736";
            $country->Title     = _t("SilvercartCountry.TITLE_SD");
            $country->Continent = "AF";
            $country->Currency  = "SDG";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SE";
            $country->ISO3      = "SWE";
            $country->FIPS      = "SW";
            $country->ISON      = "752";
            $country->Title     = _t("SilvercartCountry.TITLE_SE");
            $country->Continent = "EU";
            $country->Currency  = "SEK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SG";
            $country->ISO3      = "SGP";
            $country->FIPS      = "SN";
            $country->ISON      = "702";
            $country->Title     = _t("SilvercartCountry.TITLE_SG");
            $country->Continent = "AS";
            $country->Currency  = "SGD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SH";
            $country->ISO3      = "SHN";
            $country->FIPS      = "SH";
            $country->ISON      = "654";
            $country->Title     = _t("SilvercartCountry.TITLE_SH");
            $country->Continent = "AF";
            $country->Currency  = "SHP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SI";
            $country->ISO3      = "SVN";
            $country->FIPS      = "SI";
            $country->ISON      = "705";
            $country->Title     = _t("SilvercartCountry.TITLE_SI");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SJ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SJ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SJ";
            $country->ISO3      = "SJM";
            $country->FIPS      = "SV";
            $country->ISON      = "744";
            $country->Title     = _t("SilvercartCountry.TITLE_SJ");
            $country->Continent = "EU";
            $country->Currency  = "NOK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SK";
            $country->ISO3      = "SVK";
            $country->FIPS      = "LO";
            $country->ISON      = "703";
            $country->Title     = _t("SilvercartCountry.TITLE_SK");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SL";
            $country->ISO3      = "SLE";
            $country->FIPS      = "SL";
            $country->ISON      = "694";
            $country->Title     = _t("SilvercartCountry.TITLE_SL");
            $country->Continent = "AF";
            $country->Currency  = "SLL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SM";
            $country->ISO3      = "SMR";
            $country->FIPS      = "SM";
            $country->ISON      = "674";
            $country->Title     = _t("SilvercartCountry.TITLE_SM");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SN";
            $country->ISO3      = "SEN";
            $country->FIPS      = "SG";
            $country->ISON      = "686";
            $country->Title     = _t("SilvercartCountry.TITLE_SN");
            $country->Continent = "AF";
            $country->Currency  = "XOF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SO";
            $country->ISO3      = "SOM";
            $country->FIPS      = "SO";
            $country->ISON      = "706";
            $country->Title     = _t("SilvercartCountry.TITLE_SO");
            $country->Continent = "AF";
            $country->Currency  = "SOS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SR";
            $country->ISO3      = "SUR";
            $country->FIPS      = "NS";
            $country->ISON      = "740";
            $country->Title     = _t("SilvercartCountry.TITLE_SR");
            $country->Continent = "SA";
            $country->Currency  = "SRD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ST
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ST'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ST";
            $country->ISO3      = "STP";
            $country->FIPS      = "TP";
            $country->ISON      = "678";
            $country->Title     = _t("SilvercartCountry.TITLE_ST");
            $country->Continent = "AF";
            $country->Currency  = "STD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SV
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SV'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SV";
            $country->ISO3      = "SLV";
            $country->FIPS      = "ES";
            $country->ISON      = "222";
            $country->Title     = _t("SilvercartCountry.TITLE_SV");
            $country->Continent = "NA";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SX
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SX'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SX";
            $country->ISO3      = "SXM";
            $country->FIPS      = "NN";
            $country->ISON      = "534";
            $country->Title     = _t("SilvercartCountry.TITLE_SX");
            $country->Continent = "NA";
            $country->Currency  = "ANG";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SY";
            $country->ISO3      = "SYR";
            $country->FIPS      = "SY";
            $country->ISON      = "760";
            $country->Title     = _t("SilvercartCountry.TITLE_SY");
            $country->Continent = "AS";
            $country->Currency  = "SYP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country SZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='SZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "SZ";
            $country->ISO3      = "SWZ";
            $country->FIPS      = "WZ";
            $country->ISON      = "748";
            $country->Title     = _t("SilvercartCountry.TITLE_SZ");
            $country->Continent = "AF";
            $country->Currency  = "SZL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TC
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TC'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TC";
            $country->ISO3      = "TCA";
            $country->FIPS      = "TK";
            $country->ISON      = "796";
            $country->Title     = _t("SilvercartCountry.TITLE_TC");
            $country->Continent = "NA";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TD
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TD'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TD";
            $country->ISO3      = "TCD";
            $country->FIPS      = "CD";
            $country->ISON      = "148";
            $country->Title     = _t("SilvercartCountry.TITLE_TD");
            $country->Continent = "AF";
            $country->Currency  = "XAF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TF";
            $country->ISO3      = "ATF";
            $country->FIPS      = "FS";
            $country->ISON      = "260";
            $country->Title     = _t("SilvercartCountry.TITLE_TF");
            $country->Continent = "AN";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TG";
            $country->ISO3      = "TGO";
            $country->FIPS      = "TO";
            $country->ISON      = "768";
            $country->Title     = _t("SilvercartCountry.TITLE_TG");
            $country->Continent = "AF";
            $country->Currency  = "XOF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TH
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TH'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TH";
            $country->ISO3      = "THA";
            $country->FIPS      = "TH";
            $country->ISON      = "764";
            $country->Title     = _t("SilvercartCountry.TITLE_TH");
            $country->Continent = "AS";
            $country->Currency  = "THB";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TJ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TJ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TJ";
            $country->ISO3      = "TJK";
            $country->FIPS      = "TI";
            $country->ISON      = "762";
            $country->Title     = _t("SilvercartCountry.TITLE_TJ");
            $country->Continent = "AS";
            $country->Currency  = "TJS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TK";
            $country->ISO3      = "TKL";
            $country->FIPS      = "TL";
            $country->ISON      = "772";
            $country->Title     = _t("SilvercartCountry.TITLE_TK");
            $country->Continent = "OC";
            $country->Currency  = "NZD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TL
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TL'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TL";
            $country->ISO3      = "TLS";
            $country->FIPS      = "TT";
            $country->ISON      = "626";
            $country->Title     = _t("SilvercartCountry.TITLE_TL");
            $country->Continent = "OC";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TM";
            $country->ISO3      = "TKM";
            $country->FIPS      = "TX";
            $country->ISON      = "795";
            $country->Title     = _t("SilvercartCountry.TITLE_TM");
            $country->Continent = "AS";
            $country->Currency  = "TMT";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TN";
            $country->ISO3      = "TUN";
            $country->FIPS      = "TS";
            $country->ISON      = "788";
            $country->Title     = _t("SilvercartCountry.TITLE_TN");
            $country->Continent = "AF";
            $country->Currency  = "TND";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TO
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TO'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TO";
            $country->ISO3      = "TON";
            $country->FIPS      = "TN";
            $country->ISON      = "776";
            $country->Title     = _t("SilvercartCountry.TITLE_TO");
            $country->Continent = "OC";
            $country->Currency  = "TOP";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TR
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TR'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TR";
            $country->ISO3      = "TUR";
            $country->FIPS      = "TU";
            $country->ISON      = "792";
            $country->Title     = _t("SilvercartCountry.TITLE_TR");
            $country->Continent = "AS";
            $country->Currency  = "TRY";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TT";
            $country->ISO3      = "TTO";
            $country->FIPS      = "TD";
            $country->ISON      = "780";
            $country->Title     = _t("SilvercartCountry.TITLE_TT");
            $country->Continent = "NA";
            $country->Currency  = "TTD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TV
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TV'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TV";
            $country->ISO3      = "TUV";
            $country->FIPS      = "TV";
            $country->ISON      = "798";
            $country->Title     = _t("SilvercartCountry.TITLE_TV");
            $country->Continent = "OC";
            $country->Currency  = "AUD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TW";
            $country->ISO3      = "TWN";
            $country->FIPS      = "TW";
            $country->ISON      = "158";
            $country->Title     = _t("SilvercartCountry.TITLE_TW");
            $country->Continent = "AS";
            $country->Currency  = "TWD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country TZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='TZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "TZ";
            $country->ISO3      = "TZA";
            $country->FIPS      = "TZ";
            $country->ISON      = "834";
            $country->Title     = _t("SilvercartCountry.TITLE_TZ");
            $country->Continent = "AF";
            $country->Currency  = "TZS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country UA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='UA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "UA";
            $country->ISO3      = "UKR";
            $country->FIPS      = "UP";
            $country->ISON      = "804";
            $country->Title     = _t("SilvercartCountry.TITLE_UA");
            $country->Continent = "EU";
            $country->Currency  = "UAH";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country UG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='UG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "UG";
            $country->ISO3      = "UGA";
            $country->FIPS      = "UG";
            $country->ISON      = "800";
            $country->Title     = _t("SilvercartCountry.TITLE_UG");
            $country->Continent = "AF";
            $country->Currency  = "UGX";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country UM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='UM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "UM";
            $country->ISO3      = "UMI";
            $country->FIPS      = "";
            $country->ISON      = "581";
            $country->Title     = _t("SilvercartCountry.TITLE_UM");
            $country->Continent = "OC";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country US
        if (!DataObject::get('SilvercartCountry',"`ISO2`='US'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "US";
            $country->ISO3      = "USA";
            $country->FIPS      = "US";
            $country->ISON      = "840";
            $country->Title     = _t("SilvercartCountry.TITLE_US");
            $country->Continent = "NA";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country UY
        if (!DataObject::get('SilvercartCountry',"`ISO2`='UY'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "UY";
            $country->ISO3      = "URY";
            $country->FIPS      = "UY";
            $country->ISON      = "858";
            $country->Title     = _t("SilvercartCountry.TITLE_UY");
            $country->Continent = "SA";
            $country->Currency  = "UYU";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country UZ
        if (!DataObject::get('SilvercartCountry',"`ISO2`='UZ'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "UZ";
            $country->ISO3      = "UZB";
            $country->FIPS      = "UZ";
            $country->ISON      = "860";
            $country->Title     = _t("SilvercartCountry.TITLE_UZ");
            $country->Continent = "AS";
            $country->Currency  = "UZS";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country VA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='VA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "VA";
            $country->ISO3      = "VAT";
            $country->FIPS      = "VT";
            $country->ISON      = "336";
            $country->Title     = _t("SilvercartCountry.TITLE_VA");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country VC
        if (!DataObject::get('SilvercartCountry',"`ISO2`='VC'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "VC";
            $country->ISO3      = "VCT";
            $country->FIPS      = "VC";
            $country->ISON      = "670";
            $country->Title     = _t("SilvercartCountry.TITLE_VC");
            $country->Continent = "NA";
            $country->Currency  = "XCD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country VE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='VE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "VE";
            $country->ISO3      = "VEN";
            $country->FIPS      = "VE";
            $country->ISON      = "862";
            $country->Title     = _t("SilvercartCountry.TITLE_VE");
            $country->Continent = "SA";
            $country->Currency  = "VEF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country VG
        if (!DataObject::get('SilvercartCountry',"`ISO2`='VG'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "VG";
            $country->ISO3      = "VGB";
            $country->FIPS      = "VI";
            $country->ISON      = "092";
            $country->Title     = _t("SilvercartCountry.TITLE_VG");
            $country->Continent = "NA";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country VI
        if (!DataObject::get('SilvercartCountry',"`ISO2`='VI'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "VI";
            $country->ISO3      = "VIR";
            $country->FIPS      = "VQ";
            $country->ISON      = "850";
            $country->Title     = _t("SilvercartCountry.TITLE_VI");
            $country->Continent = "NA";
            $country->Currency  = "USD";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country VN
        if (!DataObject::get('SilvercartCountry',"`ISO2`='VN'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "VN";
            $country->ISO3      = "VNM";
            $country->FIPS      = "VM";
            $country->ISON      = "704";
            $country->Title     = _t("SilvercartCountry.TITLE_VN");
            $country->Continent = "AS";
            $country->Currency  = "VND";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country VU
        if (!DataObject::get('SilvercartCountry',"`ISO2`='VU'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "VU";
            $country->ISO3      = "VUT";
            $country->FIPS      = "NH";
            $country->ISON      = "548";
            $country->Title     = _t("SilvercartCountry.TITLE_VU");
            $country->Continent = "OC";
            $country->Currency  = "VUV";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country WF
        if (!DataObject::get('SilvercartCountry',"`ISO2`='WF'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "WF";
            $country->ISO3      = "WLF";
            $country->FIPS      = "WF";
            $country->ISON      = "876";
            $country->Title     = _t("SilvercartCountry.TITLE_WF");
            $country->Continent = "OC";
            $country->Currency  = "XPF";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country WS
        if (!DataObject::get('SilvercartCountry',"`ISO2`='WS'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "WS";
            $country->ISO3      = "WSM";
            $country->FIPS      = "WS";
            $country->ISON      = "882";
            $country->Title     = _t("SilvercartCountry.TITLE_WS");
            $country->Continent = "OC";
            $country->Currency  = "WST";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country XK
        if (!DataObject::get('SilvercartCountry',"`ISO2`='XK'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "XK";
            $country->ISO3      = "XKX";
            $country->FIPS      = "KV";
            $country->ISON      = "0";
            $country->Title     = _t("SilvercartCountry.TITLE_XK");
            $country->Continent = "EU";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country YE
        if (!DataObject::get('SilvercartCountry',"`ISO2`='YE'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "YE";
            $country->ISO3      = "YEM";
            $country->FIPS      = "YM";
            $country->ISON      = "887";
            $country->Title     = _t("SilvercartCountry.TITLE_YE");
            $country->Continent = "AS";
            $country->Currency  = "YER";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country YT
        if (!DataObject::get('SilvercartCountry',"`ISO2`='YT'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "YT";
            $country->ISO3      = "MYT";
            $country->FIPS      = "MF";
            $country->ISON      = "175";
            $country->Title     = _t("SilvercartCountry.TITLE_YT");
            $country->Continent = "AF";
            $country->Currency  = "EUR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ZA
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ZA'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ZA";
            $country->ISO3      = "ZAF";
            $country->FIPS      = "SF";
            $country->ISON      = "710";
            $country->Title     = _t("SilvercartCountry.TITLE_ZA");
            $country->Continent = "AF";
            $country->Currency  = "ZAR";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ZM
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ZM'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ZM";
            $country->ISO3      = "ZMB";
            $country->FIPS      = "ZA";
            $country->ISON      = "894";
            $country->Title     = _t("SilvercartCountry.TITLE_ZM");
            $country->Continent = "AF";
            $country->Currency  = "ZMK";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }

            // write country ZW
        if (!DataObject::get('SilvercartCountry',"`ISO2`='ZW'")) {
            $country = new SilvercartCountry();
            $country->ISO2      = "ZW";
            $country->ISO3      = "ZWE";
            $country->FIPS      = "ZI";
            $country->ISON      = "716";
            $country->Title     = _t("SilvercartCountry.TITLE_ZW");
            $country->Continent = "AF";
            $country->Currency  = "ZWL";
            $country->Locale    = Translatable::get_current_locale();
            $country->write();
       }
