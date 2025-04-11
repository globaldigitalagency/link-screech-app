FROM globaldigitalagency/sample:8.3

# Switch to root for installations
USER root

# Update package list and install font-related dependencies
RUN echo "deb http://deb.debian.org/debian bullseye main contrib" > /etc/apt/sources.list && \
    echo "deb http://deb.debian.org/debian-security bullseye-security main contrib" >> /etc/apt/sources.list && \
    apt update && \
    DEBIAN_FRONTEND=noninteractive apt install -y ttf-mscorefonts-installer cabextract wget

# Download the Screaming Frog .deb package
RUN wget https://download.screamingfrog.co.uk/products/seo-spider/screamingfrogseospider_19.8_all.deb

# Install Screaming Frog from the downloaded .deb file
RUN apt install -y ./screamingfrogseospider_19.8_all.deb

# Clean up downloaded file (optional, to reduce image size)
RUN rm -f screamingfrogseospider_19.8_all.deb

# Create config directory and accept EULA
RUN mkdir -p /home/www-data/.ScreamingFrogSEOSpider && \
    echo "accepted_eula=true" > /home/www-data/.ScreamingFrogSEOSpider/spider.config && \
    cat ./var/licence.txt >> /home/www-data/.ScreamingFrogSEOSpider/licence.txt && \
    chown -R www-data:www-data /home/www-data/.ScreamingFrogSEOSpider

# Switch to www-data user
USER www-data