import { DefineLocaleMessage } from "vue-i18n";
import en_US from "./locales/en_US.json";

type MessageSchema = typeof en_US;

declare module "vue-i18n" {
  // define the locale messages schema
  export interface DefineLocaleMessage extends MessageSchema {}
}
