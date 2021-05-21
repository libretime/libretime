#/bin/bash
echo "::group::Install Python apps"
pip3 install nose mock

for app in `ls python_apps`; do
  pip3 install -e python_apps/$app
done
echo "::endgroup::"
