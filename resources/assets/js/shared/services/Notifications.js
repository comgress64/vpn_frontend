import noty from 'noty';

export function notySuccess(text, timeout = 3000) {
  noty({
    type: 'success',
    layout: 'topRight',
    text,
    timeout,
  });
}

export function notyError(text, timeout = 3000) {
  noty({
    type: 'error',
    layout: 'topRight',
    text,
    timeout,
  });
}
