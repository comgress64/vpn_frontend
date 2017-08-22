import { notyError } from '_shared/services/Notifications';

export default function (response) {
  if (response instanceof Error) {
    throw response;
  }
  if (Object.isObject(response.body)) {
    if (response.body.error != null) {
      notyError(response.body.error);
    }
    else if (response.body.errors != null) {
      if (!Object.isArray(response.body.errors)) {
        throw new Error('[catcherNotifier] Errors response should be array');
      }
      response.body.errors.forEach(notyError);
    }
  }
  else if (response.body != null) {
    notyError(response.body);
  }
  else {
    notyError(response);
  }
}
