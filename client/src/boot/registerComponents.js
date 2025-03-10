import Injector from 'lib/Injector';
import EmbedField from "components/EmbedField/EmbedField";

export default () => {
  Injector.component.registerMany({
      EmbedField,
  });
};
