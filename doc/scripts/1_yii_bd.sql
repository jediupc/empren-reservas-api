CREATE DATABASE yii_reserves;
CREATE USER 'yii_u_reserves'@'localhost' IDENTIFIED BY 'yii_p_reserves';
GRANT SELECT,INSERT,UPDATE ON yii_reserves.* TO 'yii_u_reserves'@'localhost' IDENTIFIED BY 'yii_p_reserves';
GRANT DELETE ON yii_reserves.* TO 'yii_u_reserves'@'localhost' IDENTIFIED BY 'yii_p_reserves';
